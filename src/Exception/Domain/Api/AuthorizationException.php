<?php

namespace App\Exception\Domain\Api;

use App\Model\Response\Api\ApiResponse;

class AuthorizationException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Not authorized action';

    /**
     * @var int
     */
    protected $code = ApiResponse::HTTP_UNAUTHORIZED;
}