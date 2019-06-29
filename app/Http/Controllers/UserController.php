<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\Common\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Token;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

class UserController extends Controller
{
    /**
     * Instance of UserRepository
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Instanceof UserTransformer
     *
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     * @param UserTransformer $userTransformer
     */
    public function __construct(UserRepository $userRepository, UserTransformer $userTransformer)
    {
        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->findBy($request->all());

        return $this->respondWithCollection($users, $this->userTransformer, 'users');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id = 'me')
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $user);

        if ($id === 'me') {
            $this->fractal->parseIncludes('permissions');
        }

        return $this->respondWithItem($user, $this->userTransformer, 'users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->save($request->all());

        if (!$user instanceof User) {
            return $this->sendCustomResponse(500, 'Error occurred on creating user');
        }

        return $this->setStatusCode(201)->respondWithItem($user, $this->userTransformer, 'users');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $user);

        // Validation
        $validatorResponse = $this->validateRulesForUpdate($request, $this->storeRequestValidationRules(), $id);

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->update($user, $request->all());

        return $this->respondWithItem($user, $this->userTransformer, 'users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('destroy', $user);

        $this->userRepository->delete($user);

        return $this->sendEmptyDataResponse();
    }

    /**
     * Update password for user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function changePassword(Request $request)
    {

        // Get current logged-in user
        $user = user();

        // Validation
        $validateData = [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ];
        $validatorResponse = $this->validateRequest($request, $validateData);
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        // The passwords matches
        if (Hash::check($request->get('current_password'), $user->password) !== true) {
            return $this->sendErrorResponse("Your current password does not matches with the password you provided. Please try again.");
        }

        // Current password and new password are same
        if (strcmp($request->get('current_password'), $request->get('new_password')) === 0) {
            return $this->sendErrorResponse("New Password cannot be same as your current password. Please choose a different password.");
        }

        // Set new password
        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        // Current token
        $currentToken = $user->token()->id;

        // Revoke token
        $user->token()->revoke();

        // Revoke refresh token
        app(RefreshTokenRepository::class)->revokeRefreshToken($currentToken);

        // Send new token
        /** @var ClientRepository $clientRepository */
        $clientRepository = app(ClientRepository::class);
        $defaultClient = $clientRepository->findActive(1);
        if (!$defaultClient) {
            return $this->sendErrorResponse('No client found, error from API');
        }

        $inputs = [
            'username' => $user->email,
            'password' => $request->get('new_password'),
            'scope' => "*",
            'grant_type' => 'password',
            'client_id' => $defaultClient->id,
            'client_secret' => $defaultClient->secret
        ];

        $tokenRequest = (new DiactorosFactory)->createRequest($request->merge($inputs));

        /** @var AuthorizationServer $authorizationServer */
        $authorizationServer = app(AuthorizationServer::class);
        $tokenResponse = $authorizationServer->respondToAccessTokenRequest($tokenRequest, new Psr7Response);

        return $tokenResponse;
    }

    /**
     * Revoke the user' access tokens
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function logout($id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Revoke user tokens
        $user->tokens()->get()->each(function (Token $token) {
            $token->revoke();
        });
        return $this->sendEmptyDataResponse();
    }

    /**
     * Update password with admin privileges
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     */
    public function changePasswordForAdmin(Request $request, $id)
    {
        // Get current logged-in user is not admin
        if (!user()->isAdmin()) {
            return $this->sendForbiddenResponse();
        }

        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Validation
        $validateData = [
            'password' => 'required|string|min:8',
        ];

        $validatorResponse = $this->validateRequest($request, $validateData);
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        // Set new password
        $user->password = Hash::make($request->get('password'));
        $user->save();

        // Revoke user tokens
        $user->tokens()->get()->each(function (Token $token) {
            $token->revoke();
        });

        return $this->sendEmptyDataResponse();
    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */
    private function storeRequestValidationRules()
    {
        $rules = [
            'name' => 'required|max:255',
            'username' => 'max:100',
            'email' => 'email|required|max:255|unique:users',
            'password' => 'required|min:5|max:255',
            'gender' => 'numeric|min:1|max:3',
            'address' => 'max:255',
            'phone' => 'max:45',
            'profile_picture' => 'max:255',
            'roles' => 'required|array|in:' . implode(',', config('common.users.roles')),
            'is_active' => 'required|numeric|min:0|max:1',
        ];

        return $rules;
    }
}
