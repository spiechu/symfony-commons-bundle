<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\DataCollector;

class SchemaValidationTest extends WebTestCase
{
    public function testResponseWasValidated()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/annotated/simple-json?id=123');

        self::assertSame(['id' => '123'], json_decode($client->getResponse()->getContent(), true));

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->responseWasChecked());
    }
}
