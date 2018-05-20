<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Reader;

trait ControllerAnnotationExtractorTrait
{
    /**
     * @return Reader
     */
    abstract protected function getAnnotationReader(): Reader;

    /**
     * @param callable $controller
     * @param string   $annotationClass
     *
     * @return null|object
     */
    protected function getClassAnnotationFromController(callable $controller, string $annotationClass)
    {
        if (\is_object($objectToReflect = $this->getObjectToReflect($controller))) {
            return $this->getAnnotationReader()->getClassAnnotation(
                new \ReflectionObject($objectToReflect),
                $annotationClass
            );
        }

        return null;
    }

    /**
     * @param callable $controller
     * @param string   $annotationClass
     *
     * @throws \ReflectionException When $controller method does not exist
     *
     * @return null|object
     */
    protected function getMethodAnnotationFromController(callable $controller, string $annotationClass)
    {
        if (\is_array($controller)
            && isset($controller[1])
            && \is_object($objectToReflect = $this->getObjectToReflect($controller))
        ) {
            return $this->getAnnotationReader()->getMethodAnnotation(
                new \ReflectionMethod($objectToReflect, $controller[1]),
                $annotationClass
            );
        }

        return null;
    }

    /**
     * @param callable $controller
     *
     * @return null|object
     */
    protected function getObjectToReflect(callable $controller): ?object
    {
        if (\is_array($controller) && isset($controller[0]) && \is_object($controller[0])) {
            return $controller[0];
        }

        if (\is_object($controller) && !$controller instanceof \Closure) {
            return $controller;
        }

        return null;
    }
}
