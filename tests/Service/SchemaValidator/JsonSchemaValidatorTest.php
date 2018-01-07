<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Service\SchemaValidator;

use JsonSchema\Validator;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\JsonSchemaValidator;

class JsonSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonSchemaValidator
     */
    protected $jsonSchemaValidator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Validator
     */
    protected $validatorMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->validatorMock = $this->getMockBuilder(Validator::class)->disableOriginalConstructor()->getMock();

        $this->jsonSchemaValidator = new JsonSchemaValidator(new \stdClass(), $this->validatorMock);
    }

    public function testValidatorWillBeResettedBeforeValidation()
    {
        $this->validatorMock->expects($this->once())->method('reset');
        $this->validatorMock->expects($this->never())->method('check');

        $this->jsonSchemaValidator->validate('');
    }

    public function testEmptyJsonStringWillResultInValidationViolation()
    {
        $this->validatorMock->expects($this->never())->method('check');

        $validationResult = $this->jsonSchemaValidator->validate('');

        self::assertCount(1, $validationResult);
        self::assertRegExp('/not a json/i', (string) current($validationResult->getViolations()));
    }

    public function testCheckExceptionWillResultInValidationViolation()
    {
        $this->validatorMock->expects($this->once())->method('check')->willThrowException(new \LogicException('WAT'));
        $this->validatorMock->expects($this->once())->method('getErrors')->willReturn([]);

        $validationResult = $this->jsonSchemaValidator->validate('{}');

        self::assertCount(1, $validationResult);
        self::assertRegExp('/exception.{1,}wat/i', (string) current($validationResult->getViolations()));
    }
}
