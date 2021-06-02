<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class TokenExtractionException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Token exception';

    /**
     * @var int
     */
    protected $code = Response::HTTP_UNAUTHORIZED;

    /**
     * @inheritDoc
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}