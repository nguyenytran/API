<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class AuthorizationController extends AccessTokenController
{
    use ResponseTrait;

    /**
     * Instance of ClientRepository
     *
     * @var ClientRepository $clientRepository
     */
    private $clientRepository;

    /**
     * Instance of TokenRepository
     *
     * @var TokenRepository $tokenRepository
     */
    private $tokenRepository;

    /**
     * Instance of RefreshTokenRepository
     *
     * @var RefreshTokenRepository $refreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * Instance of UserRepository
     *
     * @var UserRepository
     */

    private $userRepository;

    /**
     * AuthorizationController constructor.
     * @param AuthorizationServer $server
     * @param TokenRepository $tokenRepository
     * @param JwtParser $jwt
     * @param ClientRepository $clientRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        AuthorizationServer $server,
        TokenRepository $tokenRepository,
        JwtParser $jwt,
        ClientRepository $clientRepository,
        RefreshTokenRepository $refreshTokenRepository,
        UserRepository $userRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->userRepository = $userRepository;
        parent::__construct($server, $tokenRepository, $jwt);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $defaultClient = $this->clientRepository->findActive(1);

        if (!$defaultClient) {
            return $this->sendErrorResponse('No client found, error from API');
        }

        $inputs = $request->only(['username', 'password']);

        $inputs = array_merge($inputs, [
            'scope' => "*",
            'grant_type' => 'password',
            'client_id' => $defaultClient->id,
            'client_secret' => $defaultClient->secret
        ]);

        $tokenRequest = (new DiactorosFactory)->createRequest($request->merge($inputs));

        return $this->issueToken($tokenRequest);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        if (Auth::check()) {
            $user = user();
            $this->tokenRepository->revokeAccessToken($user->token()->id);
            $this->refreshTokenRepository->revokeRefreshToken($user->token()->id);
        }

        // Always send empty data
        return $this->sendEmptyDataResponse();
    }
}
