<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\Common\UserTransformer;
use Illuminate\Http\Request;

class RegisterUserController extends Controller
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
     * RegisterUserController constructor.
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
     * Register user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(Request $request)
    {
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
        ];

        return $rules;
    }
}
