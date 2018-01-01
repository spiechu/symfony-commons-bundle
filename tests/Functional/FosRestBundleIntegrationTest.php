<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

class FosRestBundleIntegrationTest extends WebTestCase
{
    public function testBasicSerialization()
    {
        $client = static::createClient([
            'test_case' => 'FosRestBundleIncluded',
        ]);

        $client->request('GET', '/fos-rest-bundle/1.0/get-simple-view.json?property1=abc123');

        self::assertSame(['property_1' => 'abc123'], json_decode($client->getResponse()->getContent(), true));

        self::assertDataCollectorContainsProperData($client, '1.0', 0, 0);
    }
}
