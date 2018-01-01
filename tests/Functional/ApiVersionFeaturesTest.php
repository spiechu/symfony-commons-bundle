<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

class ApiVersionFeaturesTest extends WebTestCase
{
    public function testFeaturesAvailable()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/api-version-annotated/1.2/features-route');

        self::assertSame(
            ['feature_without_until', 'feature_without_since', 'feature_both'],
            json_decode($client->getResponse()->getContent(), true)
        );

        self::assertDataCollectorContainsProperData($client, '1.2', 0, 0);
    }

    public function testJsonSerializationFeatures()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/api-version-annotated/1.2/json-serialization-features-route');

        self::assertSame(
            [
                [
                    'name' => 'feature_without_until',
                    'since' => '1.0',
                    'until' => null,
                ],
                [
                    'name' => 'feature_without_since',
                    'since' => null,
                    'until' => '1.2',
                ],
                [
                    'name' => 'feature_both',
                    'since' => '1.0',
                    'until' => '1.2',
                ],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );

        self::assertDataCollectorContainsProperData($client, '1.2', 0, 0);
    }

    public function testAddFeature()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('POST', '/api-version-annotated/1.3/add-feature/test-feature/since/1.1/until');

        self::assertSame(
            [
                'added-feature' => [
                    'name' => 'test-feature',
                    'since' => '1.1',
                    'until' => null,
                ],
                'all-known-features' => [
                    [
                        'name' => 'feature_without_until',
                        'since' => '1.0',
                        'until' => null,
                    ],
                    [
                        'name' => 'feature_without_since',
                        'since' => null,
                        'until' => '1.2',
                    ],
                    [
                        'name' => 'feature_both',
                        'since' => '1.0',
                        'until' => '1.2',
                    ],
                    [
                        'name' => 'test-feature',
                        'since' => '1.1',
                        'until' => null,
                    ],
                ],
                'available-features' => [
                    [
                        'name' => 'feature_without_until',
                        'since' => '1.0',
                        'until' => null,
                    ],
                    [
                        'name' => 'test-feature',
                        'since' => '1.1',
                        'until' => null,
                    ],
                ],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );

        self::assertDataCollectorContainsProperData($client, '1.3', 0, 0);
    }
}
