<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

class ApiVersionProvider
{
    /**
     * @var null|string
     */
    protected $apiVersion;

    /**
     * @param null|string $apiVersion
     */
    public function setApiVersion(?string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @return null|string
     */
    public function getApiVersion(): ?string
    {
        return $this->apiVersion;
    }
}
