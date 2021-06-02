<?php

namespace App\Service\Domain\Validator;

interface ValidatorInterface
{
    /**
     * @param mixed $stringToCheck
     *
     * @return bool
     */
    public function isValid($stringToCheck): bool;
}