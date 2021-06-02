<?php

namespace App\Model\Request\Api\Login;

use App\Entity\User;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterSimpleUserRequest
{
    /**
     * @var User
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     *
     * @JMS\Type("App\Entity\User")
     * @JMS\Groups({"get", "create", "edit"})
     */
    protected $user;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
