<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\JsonSchemaValidatorFactory;

class JsonSchemaValidatorFactoryTest extends WebTestCase
{
    /**
     * @var JsonSchemaValidatorFactory
     */
    protected $jsonSchemaValidatorFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel = static::bootKernel([
            'test_case' => 'PublicJsonSchemaValidatorFactory',
        ]);

        $this->jsonSchemaValidatorFactory = $kernel->getContainer()->get('spiechu_symfony_commons.service.public_json_schema_validator_factory');
    }

    public function testValidationResult()
    {
        $this->jsonSchemaValidatorFactory->registerSchema(
            'json-simple',
            __DIR__ . '/app/PublicJsonSchemaValidatorFactory/json_schema/simple.json'
        );

        $validator = $this->jsonSchemaValidatorFactory->getValidator('json-simple');

        $validationResult = $validator->validate(json_encode([
            'id' => 'abc',
        ]));

        self::assertTrue($validationResult->isValid());

        $validationResult = $validator->validate(json_encode([
            'unknown' => 'abc',
        ]));

        self::assertFalse($validationResult->isValid());
    }
}
