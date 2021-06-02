<?php

namespace App\Service\Domain\Validator;

use App\Enum\Serialization\FormatEnum;

interface FactoryInterface
{
    /**
     * @param string $type
     *
     * @return ValidatorInterface
     */
    public function getValidator($type = FormatEnum::JSON_FORMAT): ValidatorInterface;
}