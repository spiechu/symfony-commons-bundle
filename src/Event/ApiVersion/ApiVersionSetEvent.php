<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ApiVersion;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Symfony\Component\EventDispatcher\Event;

class ApiVersionSetEvent extends Event
{
    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @param ApiVersion $apiVersion
     */
    public function __construct(ApiVersion $apiVersion)
    {
        $this->apiVersion = $apiVersion->getApiVersion();
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
}
