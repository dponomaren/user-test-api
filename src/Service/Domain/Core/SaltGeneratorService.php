<?php

namespace App\Service\Domain\Core;

use App\Exception\Domain\Api\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SaltGeneratorService
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var int
     */
    protected $saltSize;

    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->parameterBag = $parameterBag;
        $this->saltSize     = $this->parameterBag
            ->get('app.salt_size');

        if (0 !== $this->saltSize % 2) {
            throw new InvalidArgumentException("Salt size must be even integer, {$this->saltSize} provided");
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(intval($this->saltSize / 2)));
    }
}
