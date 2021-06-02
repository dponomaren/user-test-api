<?php

namespace App\Service\Domain\Core;

use App\Entity\User;
use App\Entity\UserToken;
use App\Exception\Domain\Api\AuthenticationException;
use App\Exception\Domain\Api\NotFoundException;
use App\Exception\Domain\Api\TokenValidationException;
use App\Exception\Domain\Api\UserBlockedException;
use App\Exception\Domain\Api\ValidationException;
use App\Model\Request\Api\Login\LoginRequestInterface;
use App\Model\Response\Api\ApiResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Manager\Core\UserTokenManager;
use App\Manager\Core\UserManager;

class AuthenticationService
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var SaltObfuscatorService
     */
    protected $saltObfuscator;

    /**
     * @var PasswordEncoderService
     */
    protected $passwordEncoder;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var UserTokenManager
     */
    protected $userTokenManager;

    /**
     * @param UserManager            $userManager
     * @param SaltObfuscatorService  $saltObfuscator
     * @param PasswordEncoderService $passwordEncoder
     * @param ValidatorInterface     $validator
     * @param UserTokenManager       $userTokenManager
     */
    public function __construct(
        UserManager            $userManager,
        SaltObfuscatorService  $saltObfuscator,
        PasswordEncoderService $passwordEncoder,
        ValidatorInterface     $validator,
        UserTokenManager       $userTokenManager
    ) {
        $this->userManager      = $userManager;
        $this->saltObfuscator   = $saltObfuscator;
        $this->passwordEncoder  = $passwordEncoder;
        $this->validator        = $validator;
        $this->userTokenManager = $userTokenManager;
    }

    /**
     * @param LoginRequestInterface $loginRequest
     *
     * @return UserToken
     *
     * @throws UserBlockedException
     * @throws ValidationException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function loginWithUsernameOrEmail(LoginRequestInterface $loginRequest): UserToken
    {
        $validationErrors = $this->validator->validate($loginRequest);

        if (0 !== $validationErrors->count()) {
            throw new ValidationException($validationErrors);
        }

        $user = $this->userManager->findUserByEmailOrUsername($loginRequest->getLoginName());

        if (!$user) {
            throw new NotFoundException('User not found.');
        }

        return $this->login($user, $loginRequest);
    }

    /**
     * @param User $user
     * @param LoginRequestInterface $loginRequest
     *
     * @return UserToken
     * @throws AuthenticationException
     * @throws ValidationException
     */
    protected function login(User $user, LoginRequestInterface $loginRequest): UserToken
    {
        try {
            $rawPassword          = $this->saltObfuscator->obfuscate($user->getSalt(), $loginRequest->getLoginPassword());
            $authenticationResult = $this->passwordEncoder->isPasswordValid($user->getPassword(), $rawPassword);

            if (false === $authenticationResult) {
                throw new AuthenticationException();
            }
        } catch (\Exception $exception) {
            throw new AuthenticationException;
        }

        return $this->userTokenManager->generateFreshForUser($user);
    }

    /**
     * @param string $userId
     * @param string $refreshToken
     *
     * @return UserToken
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function refreshUserToken(
        string $userId,
        string $refreshToken
    ): UserToken {
        /** @var UserToken $userToken */
        $userToken = $this->userTokenManager
            ->findByRefreshTokenAndId($userId, $refreshToken);

        $this->userTokenManager->invalidateUserToken($userToken);

        return $this->userTokenManager
            ->generateFreshForUser($userToken->getUser());
    }

    /**
     * @param UserToken $userToken
     *
     * @return bool
     *
     * @throws TokenValidationException
     */
    public function validateAccessTokenCredentials(UserToken $userToken): bool
    {
        if ($userToken->isInvalid()) {
            throw new TokenValidationException('Invalid access token', ApiResponse::HTTP_FORBIDDEN);
        }

        if ($userToken->getAccessTokenExpiresAt() <= (new \DateTime)) {
            throw new TokenValidationException('Access token expired', ApiResponse::HTTP_FORBIDDEN);
        }

        return true;
    }
}
