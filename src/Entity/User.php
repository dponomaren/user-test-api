<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Core\UserRepository")
 * @ORM\Table(name="auth_users", indexes={
 *     @ORM\Index(name="user_username_idx", columns={"username"}),
 *     @ORM\Index(name="user_email_email_address_idx", columns={"email"})
 * }))
 * @UniqueEntity("username")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class User extends AbstractTemporalEntity implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=60, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="60")
     * @Assert\Type("string")
     * @Assert\Regex(pattern="/^[\d\w\_\-\.]+/")
     *
     * @JMS\Groups({"get", "create"})
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="60")
     * @Assert\Type("string")
     * @Assert\Regex(pattern="/^[\d\w\_\-\.]+/")
     *
     * @JMS\Groups({"get", "create", "edit"})
     */
    protected $name;

    /**
     * @var PersistentCollection
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     *
     * @Assert\Email()
     * @Assert\NotBlank()
     * @Assert\Length(min="10", max="60")
     *
     * @JMS\Groups({"get", "create"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=6)
     *
     * @JMS\Exclude()
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     *
     * @JMS\Exclude()
     */
    protected $password;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max="4000")
     *
     * @JMS\Groups({"create"})
     * @JMS\Type("string")
     */
    protected $plainPassword;

    /**
     * @var bool
     *
     * @ORM\Column(name="blocked", type="boolean")
     *
     * @JMS\Groups({"get"})
     */
    protected $blocked = false;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserToken", mappedBy="user", cascade={"all"})
     */
    protected $tokens;

    /**
     * @var array
     *
     * @ORM\Column(name="user_roles", type="json", nullable=false)
     */
    protected $userRoles = [];

    public function __construct()
    {
        $this->tokens    = new ArrayCollection;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups({"get"})
     */
    public function getRoles(): array
    {
        if (empty($this->userRoles)) {
            return [];
        }

        return $this->userRoles;
    }

    /**
     * @return array
     */
    public function getUserRoles()
    {
        return $this->getRoles();
    }

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setUserRoles(array $roles): self
    {
        $this->userRoles = $roles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return self
     */
    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return self
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return void
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = '';
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function getBlocked(): bool
    {
        return $this->blocked;
    }

    /**
     * @param bool $blocked
     *
     * @return self
     */
    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return Collection|UserToken[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    /**
     * @param UserToken $token
     *
     * @return self
     */
    public function addToken(UserToken $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    /**
     * @param UserToken $token
     *
     * @return self
     */
    public function removeToken(UserToken $token): self
    {
        if ($this->tokens->removeElement($token)) {
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param string $plainPassword
     *
     * @return self
     */
    public function setResetPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}