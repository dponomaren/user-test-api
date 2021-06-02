<?php

namespace App\Listener\Domain;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationListener extends DomainApiListener
{
    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$this->isDocEnable() && preg_match('/\/docs/', $event->getRequest()->getUri())) {
            throw new NotFoundHttpException('Documentation is not available');
        }
    }
}