<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Manager\Core\UserRoleManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const VIEW     = 'view';
    const VIEW_ALL = 'view_all';
    const EDIT     = 'edit';
    const REMOVE   = 'remove';
    const CREATE   = 'create';

    /**
     * @var UserRoleManager
     */
    protected $userRoleManager;

    public function __construct(UserRoleManager $userRoleManager)
    {
        $this->userRoleManager = $userRoleManager;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::VIEW_ALL, self::REMOVE, self::CREATE])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        /**
         * @var User
         */
        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $currentUser);
            case self::VIEW_ALL:
                return $this->canViewAll($currentUser);
            case self::EDIT:
                return $this->canEdit($user, $currentUser);
            case self::REMOVE:
                return $this->canRemove($user, $currentUser);
            case self::CREATE:
                return $this->canCreate($currentUser);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $user, User $currentUser): bool
    {
        if (
            $this->userRoleManager->isAdmin($currentUser) ||
            $this->userRoleManager->isSuperAdmin($currentUser) ||
            $user->getId() === $currentUser->getId()
        ) {
            return true;
        }

        return false;
    }

    private function canViewAll(User $currentUser): bool
    {
        return $this->canCreate($currentUser);
    }

    private function canEdit(User $user, User $currentUser): bool
    {
        return $this->canView($user, $currentUser);
    }

    private function canRemove(User $user, User $currentUser): bool
    {
        return $this->canView($user, $currentUser);
    }

    private function canCreate(User $currentUser): bool
    {
        if (
            $this->userRoleManager->isAdmin($currentUser) ||
            $this->userRoleManager->isSuperAdmin($currentUser)
        ) {
            return true;
        }

        return false;
    }
}