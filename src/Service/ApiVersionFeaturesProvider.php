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
     * @param string      $name
     * @param null|string $since
     * @param null|string $until
     *
     * @throws \InvalidArgumentException When feature with given name already exists
     */
    public function addFeature(string $name, ?string $since, ?string $until): void
    {
        $this->assertVersionCompatibleString($since);
        $this->assertVersionCompatibleString($until);

        $this->addFeatures([
            $name => [
                'since' => $since,
                'until' => $until,
            ],
        ]);
    }

    /**
     * @param iterable $features [string featureName => array featureRange ['since' => numeric|nll, 'until' => numeric|null] ]
     *
     * @throws \InvalidArgumentException When feature with given name already exists
     */
    public function addFeatures(iterable $features): void
    {
        foreach ($features as $name => $options) {
            if (isset($this->features[$name])) {
                throw new \InvalidArgumentException(sprintf('Feature with given name "%s" already exists', $name));
            }

            $since = $options['since'] ?? null;
            $this->assertVersionCompatibleString($since);

            $until = $options['until'] ?? null;
            $this->assertVersionCompatibleString($until);

            $this->features[$name] = Definition::create($name, $since, $until);
        }
    }

    /**
     * @param string $name
     *
     * @return null|Definition
     */
    public function getFeature(string $name): ?Definition
    {
        return $this->getAllKnownFeatures()[$name] ?? null;
    }

    /**
     * @return Definition[]
     */
    public function getAllKnownFeatures(): array
    {
        return $this->features;
    }

    /**
     * @param Definition $definition
     *
     * @throws \RuntimeException When API version is not set
     *
     * @return bool
     */
    public function isFeatureAvailable(Definition $definition): bool
    {
        return isset($this->getAvailableFeatures()[$definition->getName()]) ? true : false;
    }

    /**
     * @throws \RuntimeException When API version is not set
     *
     * @return Definition[]
     */
    public function getAvailableFeatures(): array
    {
        $currentVersion = $this->apiVersionProvider->getApiVersion();

        if (null === $currentVersion) {
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

    /**
     * @param null|string $string
     *
     * @throws \InvalidArgumentException When $string is not version compatible
     */
    protected function assertVersionCompatibleString(?string $string): void
    {
        if (null === $string || is_numeric($string)) {
            return;
        }

        throw new \InvalidArgumentException(sprintf('"%s" is not version compatible string', $string));
    }
}
