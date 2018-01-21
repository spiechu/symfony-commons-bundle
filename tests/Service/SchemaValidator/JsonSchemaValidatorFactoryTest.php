<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Service\SchemaValidator;

use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\JsonSchemaValidatorFactory;
use Symfony\Component\Config\FileLocatorInterface;

class JsonSchemaValidatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonSchemaValidatorFactory
     */
    protected $jsonSchemaValidatorFactory;

    /**
     * @var FileLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileLocatorMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fileLocatorMock = $this->getMockBuilder(FileLocatorInterface::class)->getMock();

        $this->jsonSchemaValidatorFactory = new JsonSchemaValidatorFactory($this->fileLocatorMock);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /schema_1.{1,}already registered/i
     */
    public function testFactoryWillNotRegisterAlreadyRegisteredSchema()
    {
        $this->jsonSchemaValidatorFactory->registerSchema('schema_1', 'resource_1');
        $this->jsonSchemaValidatorFactory->registerSchema('schema_1', 'resource_2');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /schema_1.{1,}not registered/i
     */
    public function testFactoryWillNotCreateValidatorWithNonExistingSchema()
    {
        $this->fileLocatorMock->expects($this->never())->method($this->anything());

        $this->jsonSchemaValidatorFactory->getValidator('schema_1');
    }
}
