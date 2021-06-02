<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class UserBlockedException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'User blocked';

    /**
     * @var int
     */
    protected $code = Response::HTTP_UNAUTHORIZED;
}