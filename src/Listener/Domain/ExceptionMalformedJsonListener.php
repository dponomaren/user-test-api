<?php

namespace App\Listener\Domain;

use App\Exception\Domain\Api\ValidationException;
use App\Exception\Domain\Json\MalformedJsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionMalformedJsonListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof MalformedJsonException || !$this->isProduction()) {
            return;
        }

        $responseContent = $this->restApiService->serializeErrors($exception->getMessage(), Response::HTTP_BAD_REQUEST);

        $event->setResponse($responseContent);
    }
}