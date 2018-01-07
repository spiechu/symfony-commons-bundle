<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Twig\DataCollectorExtension;

class DataCollectorTwigExtensionTest extends WebTestCase
{
    /**
     * @var DataCollectorExtension
     */
    protected $dataCollectorExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel = static::bootKernel([
            'test_case' => 'PublicJsonSchemaValidatorFactory',
        ]);

        $this->dataCollectorExtension = $kernel->getContainer()->get('spiechu_symfony_commons.twig.public_data_collector_extension');
    }

    public function testDataCollectorExtension()
    {
        $schemaFileExistsFunction = current($this->dataCollectorExtension->getFunctions());

        self::assertInstanceOf(\Twig_SimpleFunction::class, $schemaFileExistsFunction);
        self::assertSame('schema_file_exists', $schemaFileExistsFunction->getName());

        $callable = $schemaFileExistsFunction->getCallable();

        self::assertTrue($callable('../Bundle/TestBundle/Resources/response_schema/200-simple.json'));
        self::assertFalse($callable('../Bundle/TestBundle/Resources/response_schema/non-existing.json'));
    }
}
