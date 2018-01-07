<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

class GetOverrideTest extends WebTestCase
{
    public function testDeleteMethodOverride()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/get-override/get-request');

        self::assertSame('GET request', $client->getResponse()->getContent());

        $dataCollector = static::getDataCollector($client);

        self::assertFalse($dataCollector->isGetMethodWasOverridden());
        self::assertNull($dataCollector->getGetMethodOverriddenTo());

        $client->request('GET', '/get-override/delete-request?_method=delete');

        self::assertSame('DELETE request', $client->getResponse()->getContent());

        $dataCollector = static::getDataCollector($client);

        self::assertTrue($dataCollector->isGetMethodWasOverridden());
        self::assertSame('DELETE', $dataCollector->getGetMethodOverriddenTo());
    }
}
