<?php

namespace App\Exception\Domain\Api;

use App\Model\Response\Api\ApiResponse;

class PasswordStrengthException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Password too weak';

    /**
     * @var int
     */
    protected $code = ApiResponse::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * @inheritDoc
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}