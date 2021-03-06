<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\JsonSchemaValidatorFactory;

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

        $this->jsonSchemaValidatorFactory = $kernel->getContainer()->get('spiechu_symfony_commons.service_schema_validator.public_json_schema_validator_factory');
    }

    public function testValidationResult()
    {
        $this->jsonSchemaValidatorFactory->registerSchema(
            'json-simple',
            __DIR__.'/app/PublicJsonSchemaValidatorFactory/json_schema/simple.json'
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
        self::assertCount(2, $validationResult);

        foreach ($validationResult as $validationViolation) {
            self::assertRegExp('/property/i', $validationViolation->getMessage());
            self::assertRegExp('/property/i', (string) $validationViolation);
        }
    }
}
