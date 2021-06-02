<?php

namespace App\Listener\Domain;

use App\Exception\Domain\Api\ApiException;
use App\Exception\Domain\Api\ValidationException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiExceptionListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof ApiException || !$this->isProduction()) {
            return;
        }

        $responseContent = $this->restApiService->serializeErrors($exception->getMessage(), $exception->getCode());

        $event->setResponse($responseContent);
    }
}