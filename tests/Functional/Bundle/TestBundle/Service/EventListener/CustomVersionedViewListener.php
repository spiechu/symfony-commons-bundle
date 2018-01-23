<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener;

use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\CustomVersionedView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class CustomVersionedViewListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        if (!$controllerResult instanceof CustomVersionedView) {
            return;
        }

        $event->setResponse(new Response(sprintf(
            'Response from route "%s" with version "%s" set',
            $event->getRequest()->getPathInfo(),
            $controllerResult->getVersion()
        )));
    }
}
