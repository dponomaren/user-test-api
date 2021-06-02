<?php

namespace App\Listener\Domain;

use App\Exception\Domain\Api\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $constraintViolations = $exception->getConstraintViolations();

        $responseContent = $this->restApiService->serializeErrors($constraintViolations, Response::HTTP_BAD_REQUEST);

        $event->setResponse($responseContent);
    }
}