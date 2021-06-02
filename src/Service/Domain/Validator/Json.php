<?php

namespace App\Service\Domain\Validator;

class Json implements ValidatorInterface
{
    /**
     * @param mixed $stringToCheck
     *
     * @return bool
     */
    public function isValid($stringToCheck): bool
    {
        return is_string($stringToCheck)
            && is_array(json_decode($stringToCheck, true))
            && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}