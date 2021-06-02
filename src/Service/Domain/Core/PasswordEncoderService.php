<?php

namespace App\Service\Domain\Core;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordEncoderService
{
    /**
     * @var PasswordEncoderInterface
     */
    protected $encoder;

    /**
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoder = $encoderFactory->getEncoder(User::class);
    }

    /**
     * @param string $plainPassword
     * @param string $salt
     *
     * @return string
     */
    public function encode(string $plainPassword, string $salt = ''): string
    {
        return $this->encoder->encodePassword($plainPassword, $salt);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     *
     * @return bool
     */
    public function isPasswordValid(string $encoded, string $raw, string $salt = '')
    {
        return $this->encoder->isPasswordValid($encoded, $raw, $salt);
    }
}