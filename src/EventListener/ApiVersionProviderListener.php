<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ApiVersion\ApiVersionSetEvent;
use Spiechu\SymfonyCommonsBundle\Service\ApiVersionProvider;

class ApiVersionProviderListener
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
     * @param ApiVersionSetEvent $apiVersionSetEvent
     */
    public function onApiVersionSet(ApiVersionSetEvent $apiVersionSetEvent): void
    {
        $this->apiVersionProvider->setApiVersion($apiVersionSetEvent->getApiVersion());
    }
}
