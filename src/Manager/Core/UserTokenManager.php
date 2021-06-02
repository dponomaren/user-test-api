<?php

namespace App\Manager\Core;

use App\Entity\PersistableEntityInterface;
use App\Entity\User;
use App\Entity\UserToken;
use App\Exception\Domain\Api\NotFoundException;
use App\Manager\AbstractManager;
use App\Service\Domain\Core\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\Domain\Api\ValidationException;

class UserTokenManager extends AbstractManager
{
    /**
     * @var TokenGeneratorService
     */
    protected $tokenGenerator;

    public function __construct(
        ValidatorInterface     $validator,
        EntityManagerInterface $entityManager,
        TokenGeneratorService  $tokenGenerator
    ) {
        parent::__construct(
            $entityManager->getRepository(UserToken::class),
            $entityManager,
            $validator
        );

        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param User $user
     *
     * @return UserToken
     *
     * @throws ValidationException
     * @throws \Exception
     */
    public function generateFreshForUser(User $user): UserToken
    {
        $userToken = (new UserToken)
            ->setUser($user)
            ->setAccessToken($this->tokenGenerator->generateRandomToken())
            ->setAccessTokenExpiresAt($this->tokenGenerator->getAccessTokenExpiration())
            ->setRefreshToken($this->tokenGenerator->generateRandomToken())
            ->setRefreshTokenExpiresAt($this->tokenGenerator->getRefreshTokenExpiration())
            ->setInvalid(false)
        ;

        $this->create($userToken);

        return $userToken;
    }

    /**
     * @param string $accessToken
     *
     * @return UserToken
     *
     * @throws NotFoundException
     */
    public function findByAccessToken(string $accessToken): UserToken
    {
        $userToken = $this->getRepository()
            ->findByAccessToken($accessToken);

        if (!$userToken instanceof UserToken) {
            throw new NotFoundException;
        }

        return $userToken;
    }

    /**
     * @param int    $userId
     * @param string $refreshToken
     *
     * @return UserToken
     *
     * @throws NotFoundException
     */
    public function findByRefreshTokenAndId(int $userId, string $refreshToken): UserToken
    {
        $userToken = $this->getRepository()
            ->findByRefreshTokenAndId($userId, $refreshToken);

        if (!$userToken instanceof UserToken) {
            throw new NotFoundException;
        }

        return $userToken;
    }

    /**
     * @param string $username
     *
     * @return UserToken
     *
     * @throws NotFoundException
     */
    public function findOneActiveByUsername(string $username): UserToken
    {
        $userToken = $this->getRepository()
            ->findOneActiveByUsername($username);

        if (!$userToken instanceof UserToken) {
            throw new NotFoundException;
        }

        return $userToken;
    }

    /**
     * @param User $user
     *
     * @throws ValidationException
     */
    public function invalidateUserTokens(User $user): void
    {
        $userTokens = $user->getTokens();

        /** @var UserToken $token */
        foreach ($userTokens as $token) {
            $this->invalidateUserToken($token);
        }
    }

    /**
     * @param UserToken $userToken
     *
     * @return PersistableEntityInterface
     *
     * @throws ValidationException
     */
    public function invalidateUserToken(UserToken $userToken)
    {
        $userToken->setInvalid(true);

        return $this->validateAndSave($userToken);
    }
}
