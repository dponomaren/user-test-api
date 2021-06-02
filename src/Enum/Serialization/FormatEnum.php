<?php

namespace App\Enum\Serialization;

use App\Enum\Enum;

final class FormatEnum extends Enum
{
    const JSON_FORMAT = 'json';
    const XML_FORMAT  = 'xml';
}