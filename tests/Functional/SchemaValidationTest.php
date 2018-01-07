<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\DataCollector;

class SchemaValidationTest extends WebTestCase
{
    public function testJsonResponseWasValidated()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/schema-annotated/simple-json?id=123');

        self::assertSame(['id' => '123'], json_decode($client->getResponse()->getContent(), true));

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->responseWasChecked());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /schema violations/i
     */
    public function testInvalidJsonSchemaResult()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/schema-annotated/not-valid-json?id=123');
    }

    public function testXmlResponseWasValidated()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/schema-annotated/simple-xml?id=456');

        self::assertSame('<?xml version="1.0" encoding="UTF-8"?>
<testObjects>
    <testObject>
        <string-property>im test string property</string-property>
        <int-property>456</int-property>
    </testObject>
</testObjects>', $client->getResponse()->getContent());

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->responseWasChecked());
    }
}
