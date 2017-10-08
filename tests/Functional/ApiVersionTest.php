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
        self::assertSame('1.0', $dataCollector->getApiVersion());
    }

    /**
     * @param string $routeUri
     * @param string $expectedApiVersion
     *
     * @dataProvider getRoutesVersionsDataProvider
     */
    public function testResponseApiVersionsSet(string $routeUri, string $expectedApiVersion)
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', $routeUri);

        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);

        self::assertTrue($dataCollector->apiVersionWasSet());
        self::assertSame($expectedApiVersion, $dataCollector->getApiVersion());
    }

    /**
     * @return array
     */
    public function getRoutesVersionsDataProvider(): array
    {
        return [
            [
                '/api-version-annotated/1.0/fancy-route',
                '1.0',
            ],
            [
                '/api-version-annotated/1.1/fancy-route',
                '1.1',
            ],
            [
                '/api-version-annotated/1.2/fancy-route',
                '1.2',
            ],
            [
                '/api-version-annotated/1.3/fancy-route',
                '1.3',
            ],
        ];
    }
}
