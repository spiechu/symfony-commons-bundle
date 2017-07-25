<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Reader;

trait ControllerAnnotationExtractorTrait
{
    abstract protected function getAnnotationReader(): Reader;

    protected function getClassAnnotationFromController(callable $controller = null, string $annotationClass): ?object
    {
        $objectToReflect = $this->getObjectToReflect($controller);

        if (is_object($objectToReflect)) {
            return $this->getAnnotationReader()->getClassAnnotation(
                new \ReflectionObject($objectToReflect),
                $annotationClass
            );
        }
    }

    protected function getMethodAnnotationFromController(callable $controller = null, string $annotationClass): ?object
    {
        if (is_array($controller)
            && isset($controller[1])
            && is_object($objectToReflect = $this->getObjectToReflect($controller))
        ) {
            return $this->getAnnotationReader()->getMethodAnnotation(
                new \ReflectionMethod($objectToReflect, $controller[1]),
                $annotationClass
            );
        }
    }

    protected function getObjectToReflect(callable $controller = null): ?object
    {
        if (is_object($controller)) {
            return $controller;
        }

        if (is_array($controller) && isset($controller[0]) && is_object($controller[0])) {
            return $controller[0];
        }
    }
}
