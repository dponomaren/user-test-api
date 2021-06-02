<?php


namespace App\Exception\Domain\Api;


use Symfony\Component\HttpFoundation\Response;

class TokenValidationException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Token validation exception';

    /**
     * @var int
     */
    protected $code = Response::HTTP_BAD_REQUEST;

    /**
     * @inheritDoc
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}