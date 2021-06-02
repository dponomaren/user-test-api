<?php

namespace App\Service\Domain\Core;

use App\Exception\Domain\Api\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SaltObfuscatorService
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var int
     */
    protected $saltStartingIndex;

    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->parameterBag      = $parameterBag;
        $this->saltStartingIndex = $this->parameterBag
            ->get('app.salt_obfuscator');

//        if (0 !== $this->saltStartingIndex % 2) {
//            throw new InvalidArgumentException("Salt size must be even integer, {$this->saltStartingIndex} provided");
//        }
    }

    /**
     * @param string $salt
     * @param string $plainPassword
     *
     * @return string
     */
    public function obfuscate(string $salt, string $plainPassword): string
    {
        if (strlen($plainPassword) < $this->saltStartingIndex) {
            return $plainPassword . $salt;
        }

        return substr_replace($plainPassword, $salt, $this->saltStartingIndex, 0);
    }
}