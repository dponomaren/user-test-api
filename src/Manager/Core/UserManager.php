<?php

namespace App\Manager\Core;

use App\Enum\RoleEnum;
use App\Exception\Domain\Api\ApiException;
use App\Model\Request\Api\Login\RegisterSimpleUserRequest;
use App\Repository\Core\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\Domain\Core\PasswordStrengthService;
use App\Service\Domain\Core\PasswordEncoderService;
use App\Service\Domain\Core\SaltObfuscatorService;
use App\Service\Domain\Core\SaltGeneratorService;
use App\Exception\Domain\Api\NotFoundException;
use App\Manager\AbstractManager;
use App\Entity\User;

class UserManager extends AbstractManager
{
    /**
     * @var PasswordEncoderService
     */
    protected $passwordEncoder;

    /**
     * @var SaltGeneratorService
     */
    protected $saltGenerator;

    /**
     * @var SaltObfuscatorService
     */
    protected $saltObfuscator;

    /**
     * @var PasswordStrengthService
     */
    protected $passwordStrengthCalculator;

    /**
     * @var UserRoleManager
     */
    protected $userRoleManager;

    public function __construct(
        EntityManagerInterface  $entityManager,
        ValidatorInterface      $validator,
        PasswordEncoderService  $passwordEncoder,
        SaltGeneratorService    $saltGenerator,
        SaltObfuscatorService   $saltObfuscator,
        PasswordStrengthService $passwordStrengthCalculator,
        UserRoleManager         $userRoleManager
    ) {
        parent::__construct(
            $entityManager->getRepository(User::class),
            $entityManager,
            $validator
        );

        $this->passwordEncoder            = $passwordEncoder;
        $this->saltGenerator              = $saltGenerator;
        $this->saltObfuscator             = $saltObfuscator;
        $this->passwordStrengthCalculator = $passwordStrengthCalculator;
        $this->userRoleManager            = $userRoleManager;
    }

    /**
     * @param User $instance
     *
     * @return User
     *
     * @throws \App\Exception\Domain\Api\PasswordStrengthException
     * @throws \App\Exception\Domain\Api\ValidationException
     */
    public function create($instance)
    {
        $this->generateEncodedPassword($instance);

        return $instance;
    }

    /**
     * @param User $instance
     * @param string $plainPassword
     *
     * @return User
     *
     * @throws \App\Exception\Domain\Api\PasswordStrengthException
     * @throws \App\Exception\Domain\Api\ValidationException
     * @throws \Exception
     */
    protected function generateEncodedPassword(User $instance, string $plainPassword = ''): User
    {
        if (empty($plainPassword)) {
            $plainPassword = $instance->getPlainPassword();
        }

        $this->passwordStrengthCalculator->checkStrength($plainPassword);

        $salt = $this->saltGenerator->generate();
        $instance->setSalt($salt);

        $password        = $this->saltObfuscator->obfuscate($salt, $plainPassword);
        $encodedPassword = $this->passwordEncoder->encode($password);

        $instance->setPassword($encodedPassword);

        $this->validate($instance);

        return $instance;
    }

    /**
     * @param string $username
     *
     * @return User
     *
     * @throws NotFoundException
     */
    public function findByUsername(string $username): User
    {
        $instance = $this->getRepository()->findOneBy([
            'username' => $username,
        ]);

        if ( ! $instance instanceof User) {
            throw new NotFoundException;
        }

        return $instance;
    }

    /**
     * @param string $email
     *
     * @return User
     *
     * @throws NotFoundException
     */
    public function findByEmail(string $email): User
    {
        $instance = $this->getRepository()->findOneBy([
            'email' => $email,
        ]);

        if ( ! $instance instanceof User) {
            throw new NotFoundException;
        }

        return $instance;
    }

    /**
     * @param string $string
     *
     * @return User|null
     */
    public function findUserByEmailOrUsername(string $string)
    {
        return $this->getRepository()->findUserByEmailOrUsername($string);
    }

    /**
     * @param User   $user
     * @param string $plainPassword
     *
     * @return User
     *
     * @throws \App\Exception\Domain\Api\PasswordStrengthException
     * @throws \App\Exception\Domain\Api\ValidationException
     */
    public function resetUserPassword(User $user, string $plainPassword = ''): User
    {
        $user->setResetPlainPassword($plainPassword);
        return $this->generateEncodedPassword($user, $plainPassword);
    }

    /**
     * @param User $user
     *
     * @return User
     *
     * @throws \App\Exception\Domain\Api\ValidationException
     */
    public function markUserBlocked(User $user): User
    {
        $user->setBlocked(true);
        $this->validateAndSave($user);
        $user->eraseCredentials();

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws \App\Exception\Domain\Api\ValidationException
     */
    public function markUserUnblocked(User $user): User
    {
        $user->setBlocked(false);
        $this->validateAndSave($user);
        $user->eraseCredentials();

        return $user;
    }

    public function createUser(RegisterSimpleUserRequest $simpleUserRequest, $role = null)
    {
        $user = $simpleUserRequest->getUser();

        $this->checkIfUserExist($user);

        if (null == $role) {
            $this->userRoleManager->grantRole($user, RoleEnum::ROLE_USER);
        } else {
            $this->userRoleManager->grantRole($user, $role);
        }

        return $this->generateEncodedPassword($user);
    }

    public function checkIfUserExist(User $user)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepository();

        if ($userRepository->checkIfUserExist($user->getEmail(), $user->getUsername())) {
            throw new ApiException('User already exist.');
        }
    }

    /**
     * @param User $oldUser
     * @param User $newUser
     *
     * @return User
     */
    public function update(User $oldUser, User $newUser)
    {
        return $oldUser
            ->setPlainPassword('example') // Only for User setup STUB plain pass
            ->setName($newUser->getName());
    }

    /**
     * @param User $user
     */
    public function removeAndSave(User $user)
    {
        $this->getManager()->remove($user);
        $this->getManager()->flush();
    }
}