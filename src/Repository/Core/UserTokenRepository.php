<?php

namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;
use App\Entity\UserToken;

class UserTokenRepository extends EntityRepository
{
    /**
     * @param string $accessToken
     *
     * @return UserToken|null
     *
     * @throws \Exception
     */
    public function findByAccessToken(string $accessToken)
    {
        return $this
            ->createQueryBuilder('ut')
            ->where('ut.accessToken = :accessToken')
            ->setMaxResults(1)
            ->setParameters([
                'accessToken' => $accessToken,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param int    $userId
     * @param string $refreshToken
     *
     * @return UserToken|null
     *
     * @throws \Exception
     */
    public function findByRefreshTokenAndId(int $userId, string $refreshToken)
    {
        $currentTime = new \DateTime;

        return $this
            ->createQueryBuilder('ut')
            ->where('ut.user = :userId')
            ->andWhere('ut.invalid = false')
            ->andWhere('ut.refreshTokenExpiresAt >= :refreshTokenExpiresAt')
            ->setMaxResults(1)
            ->setParameters([
                ':userId'                => $userId,
                ':refreshToken'          => $refreshToken,
                ':refreshTokenExpiresAt' => $currentTime,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function findOneActiveByUsername(string $username)
    {
        $currentTime = new \DateTime;

        return $this
            ->createQueryBuilder('ut')
            ->join('ut.user', 'utu')
            ->where('utu.username = :username')
            ->andWhere('ut.invalid = false')
            ->andWhere('ut.accessTokenExpiresAt >= :accessTokenExpiresAt')
            ->setMaxResults(1)
            ->setParameters([
                ':username'             => $username,
                ':accessTokenExpiresAt' => $currentTime,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}