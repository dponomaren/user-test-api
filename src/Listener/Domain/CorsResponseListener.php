<?php

namespace App\Listener\Domain;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsResponseListener
{
    /**
     * @var string
     */
    protected $corsAllowedOrigin;

    /**
     * @param string $corsAllowedOrigin
     */
    public function __construct(string $corsAllowedOrigin)
    {
        $this->corsAllowedOrigin = $corsAllowedOrigin;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        $allowedHeaders = 'Authorization,Content-Type,DNT,Keep-Alive,User-Agent,'
            . 'X-Requested-With,If-Modified-Since,Cache-Control';

        $response->headers->add([
            'Access-Control-Allow-Origin'      => $this->corsAllowedOrigin,
            'Access-Control-Allow-Headers'     => $allowedHeaders,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE, PATCH, DELETE',
        ]);
    }
}