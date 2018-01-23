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

        self::assertDataCollectorContainsProperData($client, '1.0', 0, 0);
    }

    /**
     * @param string $routeUri
     * @param string $expectedApiVersion
     *
     * @dataProvider getFancyRoutesVersionDataProvider
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

        self::assertDataCollectorContainsProperData($client, $expectedApiVersion, 0, 0);
    }

    /**
     * @param string $routeUri
     * @param string $expectedApiVersion
     *
     * @dataProvider getRoutesWithCustomVersionedViewDataProvider
     */
    public function testCustomVersionedView(string $routeUri, string $expectedApiVersion)
    {
        $client = static::createClient([
            'test_case' => 'TestBundleIncluded',
        ]);

        $client->request('GET', $routeUri);

        self::assertRegExp(
            sprintf(
                '/response from.{1,}%s.{1,}%d/i',
                preg_quote($routeUri, '/'),
                $expectedApiVersion
            ),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @return array
     */
    public function getFancyRoutesVersionDataProvider(): array
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

    /**
     * @return array
     */
    public function getRoutesWithCustomVersionedViewDataProvider(): array
    {
        return [
            [
                '/api-version-annotated/1.0/custom-versioned-view',
                '1.0',
            ],
            [
                '/api-version-annotated/1.1/custom-versioned-view',
                '1.1',
            ],
            [
                '/api-version-annotated/1.2/custom-versioned-view',
                '1.2',
            ],
            [
                '/api-version-annotated/1.3/custom-versioned-view',
                '1.3',
            ],
        ];
    }
}
