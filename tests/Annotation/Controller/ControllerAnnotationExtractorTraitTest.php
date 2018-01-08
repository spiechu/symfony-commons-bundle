<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Annotation\Controller;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ControllerAnnotationExtractorTrait;

class InvokableObject
{
    /**
     * @var bool
     */
    private $wasInvoked = false;

    public function __invoke()
    {
        $this->wasInvoked = true;
    }

    /**
     * @return bool
     */
    public function wasInvoked(): bool
    {
        return $this->wasInvoked;
    }
}

class ControllerAnnotationExtractorTraitTest extends \PHPUnit_Framework_TestCase
{
    use ControllerAnnotationExtractorTrait;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Reader
     */
    protected $readerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->readerMock = $this->getMockBuilder(Reader::class)->getMock();
    }

    public function testArrayBasedControllerExtraction()
    {
        self::assertSame($this, $this->getObjectToReflect([$this, 'testArrayBasedControllerExtraction']));
    }

    public function testInvokableObjectExtraction()
    {
        $invokableObject = new InvokableObject();

        self::assertSame($invokableObject, $this->getObjectToReflect($invokableObject));
    }

    public function testClosureWillNotBeTreatedAsObjectToExtract()
    {
        self::assertNull($this->getObjectToReflect(static function () {
        }));
    }

    public function testNonObjectCallableExtraction()
    {
        self::assertNull($this->getObjectToReflect('\intval'));
    }

    /**
     * @return Reader
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->readerMock;
    }
}
