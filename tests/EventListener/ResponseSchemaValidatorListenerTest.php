<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\EventListener\ResponseSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\Service\ValidationResult;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseSchemaValidatorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResponseSchemaValidatorListener
     */
    protected $testedListener;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->testedListener = new ResponseSchemaValidatorListener($this->eventDispatcherMock, true);
    }

    public function testHappyFlow()
    {
        $request = Request::create('/');
        $response = Response::create('fancy content');
        $validationResult = new ValidationResult();

        $filterResponseEvent = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $request->attributes->set(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS, [
            'json' => [
                200 => 'my-fancy-validator',
            ],
        ]);

        $response->headers->set('content_type', 'application/json');

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('hasListeners')
            ->with('spiechu_symfony_commons.event.response_schema_check.check_schema_json')
            ->willReturn(true);

        $this->eventDispatcherMock
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('spiechu_symfony_commons.event.response_schema_check.check_schema_json', self::isInstanceOf(CheckRequest::class))
            ->will(self::returnCallback(function (string $eventName, CheckRequest $checkRequest) use ($validationResult) {
                $checkRequest->setValidationResult($validationResult);
            }));

        $this->eventDispatcherMock
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('spiechu_symfony_commons.event.response_schema_check.check_result', self::isInstanceOf(CheckResult::class))
            ->will(self::returnCallback(function (string $eventName, CheckResult $checkResult) use ($validationResult) {
                self::assertSame('fancy content', $checkResult->getContent());
                self::assertSame('json', $checkResult->getFormat());
                self::assertSame($validationResult, $checkResult->getValidationResult());
            }));

        $this->testedListener->onKernelResponse($filterResponseEvent);
    }

    public function testListenerWontTriggerOnNonMasterRequest()
    {
        $request = Request::create('/');
        $response = Response::create('fancy content');

        $filterResponseEvent = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $response
        );

        $request->attributes->set(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS, [
            'json' => [
                200 => 'my-fancy-validator',
            ],
        ]);

        $response->headers->set('content_type', 'application/json');

        $this->eventDispatcherMock
            ->expects($this->never())
            ->method('dispatch');

        $this->testedListener->onKernelResponse($filterResponseEvent);
    }

    /**
     * @dataProvider responseSchemasProvider
     */
    public function testListenerWontTriggerWhenSchemaNotFound(array $responseSchemas)
    {
        $request = Request::create('/');
        $response = Response::create('fancy content');

        $filterResponseEvent = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $request->attributes->set(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS, $responseSchemas);

        $response->headers->set('content_type', 'application/json');

        $this->eventDispatcherMock
            ->expects($this->never())
            ->method('dispatch');

        $this->testedListener->onKernelResponse($filterResponseEvent);
    }

    /**
     * @dataProvider badContentTypeHeaderValueProvider
     *
     * @param mixed $contentTypeHeaderValue
     */
    public function testListenerWillThrowExceptionWhenUndeterminedFormat($contentTypeHeaderValue)
    {
        $request = Request::create('/');
        $response = Response::create('fancy content');

        $filterResponseEvent = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $request->attributes->set(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS, [
            'json' => [
                200 => 'my-fancy-validator',
            ],
        ]);

        $response->headers->set('content_type', $contentTypeHeaderValue);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/response format/i');

        $this->testedListener->onKernelResponse($filterResponseEvent);
    }

    /**
     * @dataProvider badContentTypeHeaderValueProvider
     *
     * @param mixed $contentTypeHeaderValue
     */
    public function testListenerWillSilentlyIgnoreWhenUndeterminedFormatOnFlag($contentTypeHeaderValue)
    {
        $this->testedListener = new ResponseSchemaValidatorListener($this->eventDispatcherMock, false);

        $request = Request::create('/');
        $response = Response::create('fancy content');

        $filterResponseEvent = new FilterResponseEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $request->attributes->set(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS, [
            'json' => [
                200 => 'my-fancy-validator',
            ],
        ]);

        $response->headers->set('content_type', $contentTypeHeaderValue);

        $this->eventDispatcherMock
            ->expects($this->never())
            ->method('dispatch');

        $this->testedListener->onKernelResponse($filterResponseEvent);
    }

    public function responseSchemasProvider(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    [],
                ],
            ],
            [
                [
                    'xml' => [
                        200 => 'my-fancy-validator',
                    ],
                ],
            ],
            [
                [
                    'json' => [
                        400 => 'my-fancy-validator',
                    ],
                ],
            ],
            [
                [
                    'xml' => [
                        201 => 'my-fancy-xml-validator',
                        202 => 'my-fancy-xml-validator',
                    ],
                    'json' => [
                        401 => 'my-fancy-json-validator',
                        402 => 'my-fancy-json-validator',
                    ],
                ],
            ],
        ];
    }

    public function badContentTypeHeaderValueProvider(): array
    {
        return [
            [
                null,
            ],
            [
                '',
            ],
            [
                'unknown/format',
            ],
        ];
    }
}
