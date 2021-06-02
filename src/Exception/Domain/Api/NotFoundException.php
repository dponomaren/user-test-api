<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends ApiException
{
    /**
     * @var string
     */
    protected $message = 'Resource not found';

    /**
     * @var int
     */
    protected $code = Response::HTTP_NOT_FOUND;
}