<?php

namespace App\Listener\Domain;

use App\Exception\Domain\Api\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionMethodNotAllowedHttpListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof MethodNotAllowedHttpException || !$this->isProduction()) {
            return;
        }

        $responseContent = $this->restApiService->serializeErrors($exception->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED);

        $event->setResponse($responseContent);
    }
}