<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;
use Symfony\Component\Routing\RouterInterface;

class DataCollector extends BaseDataCollector implements EventSubscriberInterface
{
    protected const DATA_COLLECTOR_NAME = 'spiechu_symfony_commons.data_collector';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(RouterInterface $router, Reader $reader, Container $container)
    {
        $this->router = $router;
        $this->reader = $reader;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        $this->data['known_response_schemas'] = $request->attributes->has(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            ? $request->attributes->get(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            : null;

        $this->extractRoutesData();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return static::DATA_COLLECTOR_NAME;
    }

    public function getGlobalResponseSchemas(): array
    {
        return $this->data['global_response_schemas'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::CHECK_RESULT => ['onCheckResult', 100],
        ];
    }

    public function onCheckResult(CheckResult $checkResult): void
    {
        $this->data['validation_result'] = $checkResult->getValidationResult();
    }

    public function getKnownResponseSchemas(): array
    {
        return empty($this->data['known_response_schemas']) ? [] : $this->data['known_response_schemas'];
    }

    public function getKnownResponseSchemaNumber(): int
    {
        $counter = 0;

        foreach ($this->getKnownResponseSchemas() as $format) {
            $counter += count($format);
        }

        return $counter;
    }

    public function responseWasChecked(): bool
    {
        return array_key_exists('validation_result', $this->data);
    }

    /**
     * @return ValidationViolation[]
     */
    public function getValidationErrors(): array
    {
        if (!$this->responseWasChecked()) {
            return [];
        }

        return $this->data['validation_result']->getViolations();
    }

    protected function extractRoutesData(): void
    {
        $this->data['global_response_schemas'] = [];

        foreach ($this->router->getRouteCollection() as $name => $route) {
            $defaults = $route->getDefaults();

            if (empty($defaults['_controller'])) {
                continue;
            }

            $methodAnnotation = $this->extractControllerResponseValidator($defaults['_controller']);

            if (!$methodAnnotation instanceof ResponseSchemaValidator) {
                continue;
            }

            $this->data['global_response_schemas'][] = [
                'path' => $route->getPath(),
                'name' => $name,
                'controller' => $defaults['_controller'],
                'response_schemas' => $methodAnnotation->getSchemas(),
            ];
        }
    }

    protected function extractControllerResponseValidator(string $controllerDefinition): ?ResponseSchemaValidator
    {
        [$controllerService, $controllerMethod] = explode(':', $controllerDefinition, 2);

        $reflectedMethod = new \ReflectionMethod($this->container->get($controllerService), ltrim($controllerMethod, ':'));

        return $this->reader->getMethodAnnotation($reflectedMethod, ResponseSchemaValidator::class);
    }
}
