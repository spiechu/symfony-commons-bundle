<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

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

        if (class_exists('FOS\RestBundle\View\View') && $controllerResult instanceof FOS\RestBundle\View\View) {
            $serializationContext = $controllerResult->getSerializationContext();

            $serializationContext->setVersion($apiVersion);

            $controllerResult->setSerializationContext($serializationContext);
        }
    }
}
