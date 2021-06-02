<?php

namespace App\Service\Domain\Validator;

use App\Enum\Serialization\FormatEnum;

class Factory implements FactoryInterface
{
    public function getValidator($type = FormatEnum::JSON_FORMAT): ValidatorInterface
    {
        if (FormatEnum::isValidValue($type) && $type = FormatEnum::JSON_FORMAT) {
            return new Json();
        }

        /** TODO: add availability add  Xml Validator*/
    }
}