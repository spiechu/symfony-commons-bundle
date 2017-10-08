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
    }
}
