<?php

namespace App\Service\Domain\Core;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TokenGeneratorService
{
    /**
     * @var int
     */
    protected $tokenSize;

    /**
     * @var int
     */
    protected $resetTokenSize;

    /**
     * @var int
     */
    protected $accessTokenHours;

    /**
     * @var int
     */
    protected $refreshTokenHours;

    /**
     * @var int
     */
    protected $resetPasswordTokenHours;

    /**
     * @var int
     */
    protected $emailValidationTokenHours;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->validateParameterValues(
            $parameterBag->get('app.token_generator.token_size'),
            $parameterBag->get('app.token_generator.reset_token_size'),
            $parameterBag->get('app.token_generator.access_token_hours'),
            $parameterBag->get('app.token_generator.refresh_token_hours'),
            $parameterBag->get('app.token_generator.email_validation_token_hours'),
            $parameterBag->get('app.token_generator.reset_password_token_hours')
        );

        $this->tokenSize                 = $parameterBag->get('app.token_generator.token_size');
        $this->resetTokenSize            = $parameterBag->get('app.token_generator.reset_token_size');
        $this->accessTokenHours          = $parameterBag->get('app.token_generator.access_token_hours');
        $this->refreshTokenHours         = $parameterBag->get('app.token_generator.refresh_token_hours');
        $this->emailValidationTokenHours = $parameterBag->get('app.token_generator.email_validation_token_hours');
        $this->resetPasswordTokenHours   = $parameterBag->get('app.token_generator.reset_password_token_hours');
    }

    /**
     * @param int $tokenSize
     * @param int $resetTokenSize
     * @param int $accessTokenHours
     * @param int $refreshTokenHours
     * @param int $emailValidationTokenHours
     * @param int $resetPasswordTokenHours
     *
     * @throws \InvalidArgumentException
     */
    private function validateParameterValues(
        int $tokenSize,
        int $resetTokenSize,
        int $accessTokenHours,
        int $refreshTokenHours,
        int $emailValidationTokenHours,
        int $resetPasswordTokenHours
    ): void {
        if (0 !== $tokenSize % 2) {
            throw new \InvalidArgumentException(
                "Token size must be an even number, {$tokenSize} provided"
            );
        }

        if (0 !== $resetTokenSize % 2) {
            throw new \InvalidArgumentException(
                "Reset token size must be an even number, {$resetTokenSize} provided"
            );
        }

        if (0 >= $tokenSize) {
            throw new \InvalidArgumentException(
                "Token size must be a positive number, {$tokenSize} provided"
            );
        }

        if (0 >= $accessTokenHours) {
            throw new \InvalidArgumentException(
                "Access token hours must be a positive number, {$accessTokenHours} provided"
            );
        }

        if (0 >= $refreshTokenHours) {
            throw new \InvalidArgumentException(
                "Refresh token hours must be a positive number, {$refreshTokenHours} provided"
            );
        }

        if (0 >= $emailValidationTokenHours) {
            throw new \InvalidArgumentException(
                "Email validation token hours must be a positive number, {$emailValidationTokenHours} provided"
            );
        }

        if (0 >= $resetPasswordTokenHours) {
            throw new \InvalidArgumentException(
                "Reset password token hours must be a positive number, {$resetPasswordTokenHours} provided"
            );
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function generateRandomToken(): string
    {
        return bin2hex(random_bytes(intval($this->tokenSize / 2)));
    }

    /**
     * Generate a reset password token.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateResetToken(): string
    {
        return bin2hex(random_bytes(intval($this->resetTokenSize / 2)));
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getAccessTokenExpiration(): \DateTime
    {
        return (new \DateTime)
            ->modify("+{$this->accessTokenHours} hours");
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getRefreshTokenExpiration(): \DateTime
    {
        return (new \DateTime)
            ->modify("+{$this->refreshTokenHours} hours");
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getEmailValidationTokenExpiration(): \DateTime
    {
        return (new \DateTime)
            ->modify("+{$this->emailValidationTokenHours} hours");
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getResetTokenExpiration(): \DateTime
    {
        return (new \DateTime)
            ->modify("+{$this->resetPasswordTokenHours} hours");
    }
}