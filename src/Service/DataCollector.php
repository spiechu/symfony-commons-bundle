<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ControllerAnnotationExtractorTrait;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Spiechu\SymfonyCommonsBundle\Event\ApiVersion\ApiVersionSetEvent;
use Spiechu\SymfonyCommonsBundle\Event\ApiVersion\Events as ApiVersionEvents;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events as ResponseSchemaCheckEvents;
use Spiechu\SymfonyCommonsBundle\EventListener\GetMethodOverrideListener;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\ValidationViolation;
use Spiechu\SymfonyCommonsBundle\Twig\DataCollectorExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class DataCollector extends BaseDataCollector implements EventSubscriberInterface
{
    use ControllerAnnotationExtractorTrait;

    public const COLLECTOR_NAME = 'spiechu_symfony_commons.data_collector';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var ControllerResolverInterface
     */
    protected $controllerResolver;

    /**
     * @var DataCollectorExtension
     */
    protected $dataCollectorExtension;

    /**
     * @param RouterInterface             $router
     * @param Reader                      $reader
     * @param ControllerResolverInterface $controllerResolver
     * @param DataCollectorExtension      $dataCollectorExtension
     */
    public function __construct(
        RouterInterface $router,
        Reader $reader,
        ControllerResolverInterface $controllerResolver,
        DataCollectorExtension $dataCollectorExtension
    ) {
        $this->router = $router;
        $this->reader = $reader;
        $this->controllerResolver = $controllerResolver;
        $this->dataCollectorExtension = $dataCollectorExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        $this->data['known_route_response_schemas'] = $request->attributes->has(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            ? $request->attributes->get(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            : null;

        $this->data['get_method_override'] = $request->attributes->has(GetMethodOverrideListener::ATTRIBUTE_REQUEST_GET_METHOD_OVERRIDE)
            ? $request->attributes->get(GetMethodOverrideListener::ATTRIBUTE_REQUEST_GET_METHOD_OVERRIDE)
            : null;

        $this->extractRoutesData();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return static::COLLECTOR_NAME;
    }

    /**
     * Forward compatibility with Symfony 3.4.
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return array
     */
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
            ResponseSchemaCheckEvents::CHECK_RESULT => ['onCheckResult', 100],
            ApiVersionEvents::API_VERSION_SET => ['onApiVersionSet', 100],
        ];
    }

    /**
     * @param CheckResult $checkResult
     */
    public function onCheckResult(CheckResult $checkResult): void
    {
        $this->data['validation_result'] = $checkResult->getValidationResult();
    }

    /**
     * @param ApiVersionSetEvent $apiVersionSetEvent
     */
    public function onApiVersionSet(ApiVersionSetEvent $apiVersionSetEvent): void
    {
        $this->data['api_version_set'] = $apiVersionSetEvent->getApiVersion();
    }

    /**
     * @return array
     */
    public function getKnownRouteResponseSchemas(): array
    {
        return empty($this->data['known_route_response_schemas']) ? [] : $this->data['known_route_response_schemas'];
    }

    /**
     * @return int
     */
    public function getKnownRouteResponseSchemaNumber(): int
    {
        $counter = 0;

        foreach ($this->getKnownRouteResponseSchemas() as $format) {
            $counter += \count($format);
        }

        return $counter;
    }

    /**
     * @return int
     */
    public function getAllPotentialErrorsCount(): int
    {
        return \count($this->getValidationErrors()) + $this->getGlobalNonExistingSchemaFiles();
    }

    /**
     * @return bool
     */
    public function responseWasChecked(): bool
    {
        return array_key_exists('validation_result', $this->data);
    }

    /**
     * @return bool
     */
    public function apiVersionWasSet(): bool
    {
        return array_key_exists('api_version_set', $this->data);
    }

    /**
     * @return null|string
     */
    public function getApiVersion(): ?string
    {
        return $this->apiVersionWasSet() ? $this->data['api_version_set'] : null;
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

    /**
     * @return bool
     */
    public function isGetMethodWasOverridden(): bool
    {
        return empty($this->data['get_method_override']) ? false : true;
    }

    /**
     * @return null|string
     */
    public function getGetMethodOverriddenTo(): ?string
    {
        return $this->isGetMethodWasOverridden() ? $this->data['get_method_override'] : null;
    }

    protected function extractRoutesData(): void
    {
        $this->data['global_response_schemas'] = [];
        $this->data['global_non_existing_schema_files'] = 0;

        /** @var Route $route */
        /** @var string $controllerDefinition */
        /** @var ResponseSchemaValidator $responseSchemaValidator */
        foreach ($this->getRouteCollectionGenerator() as $name => [$route, $controllerDefinition, $responseSchemaValidator]) {
            $annotationSchemas = $responseSchemaValidator->getSchemas();

            $this->data['global_response_schemas'][] = [
                'path' => $route->getPath(),
                'name' => $name,
                'controller' => $controllerDefinition,
                'response_schemas' => $annotationSchemas,
            ];

            foreach ($annotationSchemas as $schemas) {
                foreach ($schemas as $schema) {
                    if (!$this->dataCollectorExtension->schemaFileExists($schema)) {
                        ++$this->data['global_non_existing_schema_files'];
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     *
     * @return \Generator string $name => [Route $route, string $controllerDefinition, ResponseSchemaValidator $methodAnnotation]
     */
    protected function getRouteCollectionGenerator(): \Generator
    {
        foreach ($this->router->getRouteCollection() as $name => $route) {
            if (empty($controllerDefinition = $route->getDefault('_controller'))) {
                continue;
            }

            $methodAnnotation = $this->extractControllerResponseValidator($controllerDefinition);
            if (!$methodAnnotation instanceof ResponseSchemaValidator) {
                continue;
            }

            yield $name => [$route, $controllerDefinition, $methodAnnotation];
        }
    }

    /**
     * @param string $controllerDefinition
     *
     * @throws \Exception
     *
     * @return null|ResponseSchemaValidator
     */
    protected function extractControllerResponseValidator(string $controllerDefinition): ?ResponseSchemaValidator
    {
        $resolvedController = $this->controllerResolver->getController(new Request(
            [],
            [],
            [
                '_controller' => $controllerDefinition,
            ]
        ));

        if (!\is_callable($resolvedController)) {
            return null;
        }

        return $this->getMethodAnnotationFromController(/* @scrutinizer ignore-type */$resolvedController, ResponseSchemaValidator::class);
    }

    /**
     * @return int
     */
    protected function getGlobalNonExistingSchemaFiles(): int
    {
        return empty($this->data['global_non_existing_schema_files']) ? 0 : \count($this->data['global_non_existing_schema_files']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->reader;
    }
}
