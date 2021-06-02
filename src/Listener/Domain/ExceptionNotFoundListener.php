<?php

namespace App\Listener\Domain;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Exception\Domain\Api\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionNotFoundListener extends DomainApiListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        /** @var ValidationException $exception */
        $exception = $event->getThrowable();

        if (!$exception instanceof NotFoundHttpException || !$this->isProduction()) {
            return;
        }

        $responseContent = $this->restApiService->serializeErrors('Not found.', Response::HTTP_NOT_FOUND);

        $event->setResponse($responseContent);
    }
}