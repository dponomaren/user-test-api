<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserToken;
use App\Exception\Domain\Api\AuthorizationException;
use App\Exception\Domain\Api\NotFoundException;
use App\Manager\Core\UserTokenManager;
use App\Model\Response\Api\ApiResponse;
use App\Service\Domain\Core\AuthenticationService;
use App\Service\Domain\Core\RestApiService;
use App\Service\Domain\Core\TokenExtractionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var TokenExtractionService
     */
    protected $tokenExtraction;

    /**
     * @var UserTokenManager
     */
    protected $tokenManager;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;


    /**
     * @var RestApiService
     */
    protected $restApiService;

    /**
     * @param TokenExtractionService $tokenExtraction
     * @param UserTokenManager       $tokenManager
     * @param AuthenticationService  $authenticationService
     * @param RestApiService         $restApiService
     */
    public function __construct(
        TokenExtractionService $tokenExtraction,
        UserTokenManager       $tokenManager,
        AuthenticationService  $authenticationService,
        RestApiService         $restApiService
    ) {
        $this->tokenExtraction       = $tokenExtraction;
        $this->tokenManager          = $tokenManager;
        $this->authenticationService = $authenticationService;
        $this->restApiService        = $restApiService;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required'
        ];

        return $this->restApiService->serializeErrors($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        $authorizationHeader = $this->tokenExtraction->extractAuthorizationHeader($request);

        if (empty($authorizationHeader)) {
            return null;
        }

        return [
            'token' => $this->tokenExtraction->extract($authorizationHeader),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $token = $this->tokenManager->findByAccessToken($credentials['token']);
            $this->authenticationService
                ->validateAccessTokenCredentials($token);

            return $token->getUser();
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!empty($credentials)) {
            /** @var User $user */
            $userTokens           = $user->getTokens();
            $validatedCredentials = false;
            $foundToken           = false;
            $tokenString          = $credentials['token'];

            /** @var UserToken $token */
            foreach ($userTokens as $token) {
                if ($token->getAccessToken() === $tokenString) {
                    $foundToken           = true;
                    $validatedCredentials = $this->authenticationService
                        ->validateAccessTokenCredentials($token);
                } else {
                    continue;
                }
            }

            if (!$foundToken) {
                throw new TokenNotFoundException('Access token not found', ApiResponse::HTTP_BAD_REQUEST);
            }

            if ($validatedCredentials && $foundToken) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new AuthorizationException();
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }
}
