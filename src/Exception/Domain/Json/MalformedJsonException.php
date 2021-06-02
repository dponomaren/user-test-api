<?php

namespace App\Exception\Domain\Json;

use Symfony\Component\HttpFoundation\Response;
use App\Exception\Domain\Api\ApiException;

class MalformedJsonException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Malformed JSON request';

    /**
     * @var int
     */
    protected $code = Response::HTTP_BAD_REQUEST;
}