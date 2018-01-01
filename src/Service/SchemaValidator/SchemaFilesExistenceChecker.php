<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

use Doctrine\Common\Annotations\Reader;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ControllerAnnotationExtractorTrait;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Symfony\Component\HttpFoundation\Request;

class SchemaFilesExistenceChecker
{
    use ControllerAnnotationExtractorTrait;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ControllerResolverInterface
     */
    protected $controllerResolver;

    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @param Reader $reader
     * @param RouterInterface $router
     * @param ControllerResolverInterface $controllerResolver
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(
        Reader $reader,
        RouterInterface $router,
        ControllerResolverInterface $controllerResolver,
        FileLocatorInterface $fileLocator
    ) {
        $this->reader = $reader;
        $this->router = $router;
        $this->controllerResolver = $controllerResolver;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @throws \LogicException If any of controllers can't be found by controller resolver
     * @throws \RuntimeException When any of defined schema files in ResponseSchemaValidator not exist
     */
    public function checkControllerResponseSchemaValidatorFiles(): void
    {
        $nonExistingFiles = [];

        foreach ($this->router->getRouteCollection()->all() as $route) {
            $controllerAttribute = $route->getDefault('_controller');
            $resolvedController = $this->controllerResolver->getController(new Request(
                [],
                [],
                [
                    '_controller' => $controllerAttribute,
                ]
            ));

            if (!\is_callable($resolvedController)) {
                continue;
            }

            $annotation = $this->getMethodAnnotationFromController($resolvedController, ResponseSchemaValidator::class);
            if (!$annotation instanceof ResponseSchemaValidator) {
                continue;
            }

            if ($nonExistingSchemaFile = $this->getNonExistingSchemaFile($annotation->getSchemas())) {
                if (!array_key_exists($routePath = $route->getPath(), $nonExistingFiles)) {
                    $nonExistingFiles[$routePath] = [];
                }
                $nonExistingFiles[$routePath][] = $nonExistingSchemaFile;
            }
        }

        if (!empty($nonExistingFiles)) {
            throw $this->createExceptionFromCorruptedFiles($nonExistingFiles);
        }
    }

    /**
     * @param array $schemas
     * @return null|string
     */
    protected function getNonExistingSchemaFile(array $schemas): ?string
    {
        foreach ($schemas as $schema) {
            foreach ($schema as $schemaLocation) {
                try {
                    $this->fileLocator->locate($schemaLocation);
                } catch (FileLocatorFileNotFoundException $e) {
                    return $schemaLocation;
                }
            }
        }

        return null;
    }

    /**
     * @param array $corruptedFiles
     * @return \RuntimeException
     */
    protected function createExceptionFromCorruptedFiles(array $corruptedFiles): \RuntimeException
    {
        $routePaths = [];

        foreach ($corruptedFiles as $routePath => $files) {
            $routePaths[] = sprintf('%s[%s]', $routePath, implode(', ', $files));
        }

        return new \RuntimeException(sprintf('Non existing response schema files set: "%s"', implode(', ', $routePaths)));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAnnotationReader(): Reader
    {
        return $this->reader;
    }
}
