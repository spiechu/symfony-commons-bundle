<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\DataCollector;

class ApiVersionTest extends WebTestCase
{
    public function testResponseWasValidated()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/api-version-annotated/1.0/fancy-route');

        self::assertSame('response from fancy route', $client->getResponse()->getContent());

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->apiVersionWasSet());
    }
}
