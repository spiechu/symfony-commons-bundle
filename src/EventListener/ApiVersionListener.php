<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ControllerAnnotationExtractorTrait;
use Spiechu\SymfonyCommonsBundle\Event\ApiVersion\ApiVersionSetEvent;
use Spiechu\SymfonyCommonsBundle\Event\ApiVersion\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ApiVersionListener
{
    use ControllerAnnotationExtractorTrait;

    protected const ATTRIBUTE_API_VERSION = 'spiechu_symfony_commons.event_listener.api_version';

    /** @var Reader */
    protected $annotationReader;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param Reader                   $annotationReader
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Reader $annotationReader, EventDispatcherInterface $eventDispatcher)
    {
        $this->annotationReader = $annotationReader;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $apiVersion = $this->getApiVersionFromController($event->getController());

        if (!$apiVersion instanceof ApiVersion) {
            return;
        }

        $event->getRequest()->attributes->set(static::ATTRIBUTE_API_VERSION, $apiVersion->getApiVersion());

        $this->eventDispatcher->dispatch(Events::API_VERSION_SET, new ApiVersionSetEvent($apiVersion));
    }

    /**
     * @param callable $controller
     *
     * @return null|ApiVersion
     */
    protected function getApiVersionFromController(callable $controller): ?ApiVersion
    {
        return $this->getClassAnnotationFromController($controller, ApiVersion::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->annotationReader;
    }
}
