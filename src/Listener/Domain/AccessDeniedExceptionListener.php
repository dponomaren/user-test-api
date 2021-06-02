<?php

namespace App\Listener\Domain;

use App\Exception\Domain\Api\ValidationException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedExceptionListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedException || !$this->isProduction()) {
            return;
        }

        $responseContent = $this->restApiService->serializeErrors($exception->getMessage(), $exception->getCode());

        $event->setResponse($responseContent);
    }
}