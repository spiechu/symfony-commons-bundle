<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use FOS\RestBundle\View\View;
use Spiechu\SymfonyCommonsBundle\Service\ApiVersionProvider;
use Spiechu\SymfonyCommonsBundle\Service\VersionedViewInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class VersionedViewListener
{
    /**
     * @var ApiVersionProvider
     */
    protected $apiVersionProvider;

    /**
     * @param ApiVersionProvider $apiVersionProvider
     */
    public function __construct(ApiVersionProvider $apiVersionProvider)
    {
        $this->apiVersionProvider = $apiVersionProvider;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $apiVersion = $this->apiVersionProvider->getApiVersion();

        if (null === $apiVersion) {
            return;
        }

        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof VersionedViewInterface) {
            $controllerResult->setVersion($apiVersion);

            return;
        }

        if (class_exists(View::class) && is_a($controllerResult, View::class)) {
            $context = $controllerResult->getContext();

            $context->setVersion(/* @scrutinizer ignore-type */$apiVersion);

            $controllerResult->setContext($context);
        }
    }
}
