<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ControllerAnnotationExtractorTrait;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestSchemaValidatorListener
{
    use ControllerAnnotationExtractorTrait;

    public const ATTRIBUTE_RESPONSE_SCHEMAS = 'spiechu_symfony_commons.event_listener.response_schemas';

    /**
     * @var Reader
     */
    protected $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $responseSchemaValidator = $this->getResponseSchemaValidator($event->getController());

        if (!$responseSchemaValidator instanceof ResponseSchemaValidator) {
            return;
        }

        $event->getRequest()->attributes->set(static::ATTRIBUTE_RESPONSE_SCHEMAS, $responseSchemaValidator->getSchemas());
    }

    /**
     * @param callable $controller
     *
     * @throws \ReflectionException
     *
     * @return null|ResponseSchemaValidator
     */
    protected function getResponseSchemaValidator(callable $controller): ?ResponseSchemaValidator
    {
        return $this->getMethodAnnotationFromController($controller, ResponseSchemaValidator::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->annotationReader;
    }
}
