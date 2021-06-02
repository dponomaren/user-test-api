<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Core\UserTokenRepository")
 * @ORM\Table(name="auth_user_token")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class UserToken extends AbstractTemporalEntity
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tokens")
     *
     * @Assert\NotNull()
     *
     * @JMS\Groups({"get"})
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=150)
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min="150", max="150")
     *
     * @JMS\Groups({"get"})
     */
    protected $accessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="refresh_token", type="string", length=150)
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min="150", max="150")
     *
     * @JMS\Groups({"get"})
     */
    protected $refreshToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="access_token_expires_at", type="datetime")
     *
     * @Assert\NotNull()
     *
     * @JMS\Type("DateTime<'Y-m-d\TH:i:sP'>")
     * @JMS\Groups({"get"})
     */
    protected $accessTokenExpiresAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="refresh_token_expires_at", type="datetime")
     *
     * @Assert\NotNull()
     *
     * @JMS\Type("DateTime<'Y-m-d\TH:i:sP'>")
     * @JMS\Groups({"get"})
     */
    protected $refreshTokenExpiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="invalid", type="boolean")
     *
     * @Assert\NotNull()
     *
     * @JMS\Groups({"get"})
     */
    protected $invalid = false;

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

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return self
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return self
     */
    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAccessTokenExpiresAt(): \DateTime
    {
        return $this->accessTokenExpiresAt;
    }

    /**
     * @param \DateTime $accessTokenExpiresAt
     *
     * @return self
     */
    public function setAccessTokenExpiresAt(\DateTime $accessTokenExpiresAt): self
    {
        $this->accessTokenExpiresAt = $accessTokenExpiresAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRefreshTokenExpiresAt(): \DateTime
    {
        return $this->refreshTokenExpiresAt;
    }

    /**
     * @param \DateTime $refreshTokenExpiresAt
     *
     * @return self
     */
    public function setRefreshTokenExpiresAt(\DateTime $refreshTokenExpiresAt): self
    {
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInvalid(): bool
    {
        return $this->invalid;
    }

    /**
     * @param bool $invalid
     *
     * @return self
     */
    public function setInvalid(bool $invalid): self
    {
        $this->invalid = $invalid;

        return $this;
    }

    public function getInvalid(): bool
    {
        return $this->invalid;
    }
}
