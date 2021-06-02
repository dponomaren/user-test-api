<?php

namespace App\Model\Request\Api\Login;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class SimpleLoginRequest implements LoginRequestInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min="2", max="60")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"get", "create"})
     */
    private $loginName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min="2", max="20")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"get", "create"})
     */
    private $loginPassword;

    /**
     * @return string
     */
    public function getLoginName(): string
    {
        return $this->loginName;
    }

    /**
     * @param string $loginName
     *
     * @return self
     */
    public function setLoginName(string $loginName): self
    {
        $this->loginName = $loginName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginPassword(): string
    {
        return $this->loginPassword;
    }

    /**
     * @param string $loginPassword
     *
     * @return self
     */
    public function setLoginPassword(string $loginPassword): self
    {
        $this->loginPassword = $loginPassword;

        return $this;
    }
}