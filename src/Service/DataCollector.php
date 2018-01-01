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
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\ValidationViolation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;
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
     * @param RouterInterface             $router
     * @param Reader                      $reader
     * @param ControllerResolverInterface $controllerResolver
     */
    public function __construct(
        RouterInterface $router,
        Reader $reader,
        ControllerResolverInterface $controllerResolver
    ) {
        $this->router = $router;
        $this->reader = $reader;
        $this->controllerResolver = $controllerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        $this->data['known_route_response_schemas'] = $request->attributes->has(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            ? $request->attributes->get(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
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

    public function allPotentialErrorsCount(): int
    {

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

    protected function extractRoutesData(): void
    {
        $this->data['global_response_schemas'] = [];

        foreach ($this->router->getRouteCollection() as $name => $route) {
            if (empty($controllerDefinition = $route->getDefault('_controller'))) {
                continue;
            }

            $methodAnnotation = $this->extractControllerResponseValidator($controllerDefinition);
            if (!$methodAnnotation instanceof ResponseSchemaValidator) {
                continue;
            }

            $this->data['global_response_schemas'][] = [
                'path' => $route->getPath(),
                'name' => $name,
                'controller' => $controllerDefinition,
                'response_schemas' => $methodAnnotation->getSchemas(),
            ];
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

        return $this->getMethodAnnotationFromController($resolvedController, ResponseSchemaValidator::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->reader;
    }
}
