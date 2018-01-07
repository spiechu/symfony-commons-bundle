<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener\ListenerReplacingGetMethodListener;

class OverridingServicesTest extends WebTestCase
{
    public function testDeleteMethodOverride()
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
}
