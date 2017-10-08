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

        $client->request('GET', '/get-override/delete-request?_method=DELETE');

        self::assertSame('DELETE request', $client->getResponse()->getContent());
    }
}
