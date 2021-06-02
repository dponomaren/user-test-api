<?php

namespace App\Manager\Core;

use App\Entity\User;
use App\Entity\UserRole;
use App\Enum\RoleEnum;
use App\Exception\Domain\Api\ApiException;
use Doctrine\Common\Collections\Collection;

class UserRoleManager
{
    /**
     * @param User $user
     * @param string $roleName
     *
     * @return bool
     */
    public function hasRole(User $user, string $roleName): bool
    {
        /** @var Collection $userRoles */
        $userRoles = $user->getUserRoles();

        foreach ($userRoles as $userRole) {
            if ($roleName == $userRole) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isUser(User $user): bool
    {
        return $this->hasRole($user, RoleEnum::ROLE_USER);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isManager(User $user): bool
    {
        return $this->hasRole($user, RoleEnum::ROLE_MANAGER);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        return $this->hasRole($user, RoleEnum::ROLE_ADMIN);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isSuperAdmin(User $user): bool
    {
        return $this->hasRole($user, RoleEnum::ROLE_SUPER_ADMIN);
    }

    /**
     * @param User $user
     * @param string $role
     *
     * @return User
     *
     * @throws \ReflectionException
     * @throws ApiException
     */
    public function grantRole(User $user, string $role): User
    {
        if (!RoleEnum::isValidName($role)) {
            throw new ApiException('Role Not found.');
        }

        $roles = $user->getRoles();

        if (!array_key_exists($role, $roles)) {
            $roles[] = $role;
            $user->setUserRoles($roles);
        }

        return $user;
    }
}
