<?php

namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;
use App\Entity\UserToken;

class UserRepository extends EntityRepository
{
    public function findUserByEmailOrUsername(string $string)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.username = :string')
            ->orWhere('u.email = :string')
            ->setParameters([
                'string' => $string
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function checkIfUserExist($email, $username)
    {
        return (bool)$this
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.username = :username')
            ->orWhere('u.email = :email')
            ->setParameters([
                'email'    => $email,
                'username' => $username
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}