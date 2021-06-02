<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\HttpFoundation\Response;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Internal error';

    /**
     * @var int
     */
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}