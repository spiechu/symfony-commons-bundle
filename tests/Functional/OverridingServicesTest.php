<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener\ListenerReplacingFailedSchemaCheckListener;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener\ListenerReplacingGetMethodListener;

class OverridingServicesTest extends WebTestCase
{
    public function testPutMethodOverrideWithCustomListener()
    {
        $client = static::createClient([
            'test_case' => 'OverridingServicesTestBundle',
        ]);

        $client->request('GET', '/get-override/get-request?_method=PUT');

        self::assertSame('GET request', $client->getResponse()->getContent());

        $listener = $client->getContainer()->get(ListenerReplacingGetMethodListener::class);

        self::assertSame('_method', $listener->arg1);
        self::assertSame(['POST', 'PUT'], $listener->arg2);
        self::assertSame('PUT', $listener->request->query->get('_method'));
    }

    public function testFailedSchemaCheckOverrideWithCustomListener()
    {
        $client = static::createClient([
            'test_case' => 'OverridingServicesTestBundle',
        ]);

        $client->request('GET', '/schema-annotated/not-valid-json?id=123');

        self::assertSame(['not-id' => '123'], json_decode($client->getResponse()->getContent(), true));

        $listener = $client->getContainer()->get(ListenerReplacingFailedSchemaCheckListener::class);

        self::assertFalse($listener->checkResult->isValid());
    }
}
