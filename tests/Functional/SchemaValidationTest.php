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
     * @expectedExceptionMessageRegExp /json.{1,}schema violations.{1,}additional properties/i
     */
    public function testInvalidJsonSchemaResult()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->catchExceptions(false);

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
        self::assertSame(1, $dataCollector->getKnownRouteResponseSchemaNumber());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /xml.{1,}schema violations.{1,}unexpected\-property/i
     */
    public function testInvalidXMLResponseErrors()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->catchExceptions(false);

        $client->request('GET', '/schema-annotated/not-valid-simple-xml?id=789');
    }

    public function testJsonResponseValidatedOnMultipleSchemas()
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', '/schema-annotated/multiple-schemas-endpoint?error=wtf-error');

        self::assertSame(['error' => 'wtf-error'], json_decode($client->getResponse()->getContent(), true));

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->responseWasChecked());
        self::assertSame(4, $dataCollector->getKnownRouteResponseSchemaNumber());
    }
}
