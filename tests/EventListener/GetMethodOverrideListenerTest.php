<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\EventListener;

use Spiechu\SymfonyCommonsBundle\EventListener\GetMethodOverrideListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class GetMethodOverrideListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $queryParamName = 'testparam';

    /**
     * @var string[]
     */
    protected $methodsToOverride = [
        'A', 'B', 'C',
    ];

    /**
     * @var GetMethodOverrideListener
     */
    protected $testedListener;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->testedListener = new GetMethodOverrideListener($this->queryParamName, $this->methodsToOverride);
    }

    public function testListenerWillRejectNonStringArguments()
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessageRegExp('/non string/i');

        new GetMethodOverrideListener($this->queryParamName, [5, '6']);
    }

    public function testListenerWillChangeGetMethod()
    {
        $request = Request::create('/?testparam=a');

        $getResponseEvent = new GetResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->testedListener->onKernelRequest($getResponseEvent);

        self::assertSame('A', $request->getMethod());
    }

    public function testListenerWontTriggerOnNonMasterRequest()
    {
        $request = Request::create('/?testparam=a');

        $getResponseEvent = new GetResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->testedListener->onKernelRequest($getResponseEvent);

        self::assertSame('GET', $request->getMethod());
    }

    public function testListenerWontChangeMethodDifferentThanGet()
    {
        $request = Request::create('/?testparam=a', 'POST');

        $getResponseEvent = new GetResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->testedListener->onKernelRequest($getResponseEvent);

        self::assertSame('POST', $request->getMethod());
    }

    public function testListenerWontChangeMethodWhenNoQueryParam()
    {
        $request = Request::create('/?differentparam=a');

        $getResponseEvent = new GetResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->testedListener->onKernelRequest($getResponseEvent);

        self::assertSame('GET', $request->getMethod());
    }

    public function testListenerWontChangeMethodWhenNotPermittedMethod()
    {
        $request = Request::create('/?testparam=D');

        $getResponseEvent = new GetResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->testedListener->onKernelRequest($getResponseEvent);

        self::assertSame('GET', $request->getMethod());
    }
}
