<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class InvalidArgumentException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Invalid parameters provided';

    /**
     * @var int
     */
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}