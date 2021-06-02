<?php

namespace App\DataFixtures\User;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Manager\Core\UserManager;
use App\Model\Request\Api\Login\RegisterSimpleUserRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const PASS          = 'test!!!777';
    const SUDO_EMAIL    = 'sudo@sudo.com';
    const SUDO_USERNAME = 'sudo';
    const SUDO_NAME     = 'Sudo';

    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        $userSudo = (new User())
            ->setName(self::SUDO_NAME)
            ->setPlainPassword(self::PASS)
            ->setUserRoles([RoleEnum::ROLE_SUPER_ADMIN])
            ->setEmail(self::SUDO_EMAIL)
            ->setUsername(self::SUDO_USERNAME)
            ->setBlocked(false)
        ;

        $this->userManager->createUser(
            (new RegisterSimpleUserRequest())
                ->setUser($userSudo)
        );
        $this->userManager->validateAndSave($userSudo);
    }
}
