<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Authentication problem';

    /**
     * @var int
     */
    protected $code = Response::HTTP_UNAUTHORIZED;
}