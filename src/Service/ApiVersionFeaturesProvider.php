<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Spiechu\SymfonyCommonsBundle\ApiFeature\Definition;

class ApiVersionFeaturesProvider
{
    /**
     * @var ApiVersionProvider
     */
    protected $apiVersionProvider;

    /**
     * @var Definition[]
     */
    protected $features;

    /**
     * @param ApiVersionProvider $apiVersionProvider
     */
    public function __construct(ApiVersionProvider $apiVersionProvider)
    {
        $this->apiVersionProvider = $apiVersionProvider;

        $this->features = [];
    }

    /**
     * @param iterable $features
     *
     * @throws \InvalidArgumentException
     */
    public function addFeatures(iterable $features): void
    {
        foreach ($features as $name => $options) {
            if (isset($this->features[$name])) {
                throw new \InvalidArgumentException(sprintf('Feature with given name "%s" already exists', $name));
            }

            $this->features[$name] = new Definition($name, $options['since'] ?? null, $options['until'] ?? null);
        }
    }

    /**
     * @throws \RuntimeException
     *
     * @return Definition[]
     */
    public function getAvailableFeatures(): array
    {
        $currentVersion = $this->apiVersionProvider->getApiVersion();

        if ($currentVersion === null) {
            throw new \RuntimeException('API version is not set');
        }

        $matchingFeatures = [];

        foreach ($this->features as $feature) {
            if ($feature->isAvailableForVersion($currentVersion)) {
                $matchingFeatures[$feature->getName()] = $feature;
            }
        }

        return $matchingFeatures;
    }
}
