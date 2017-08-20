<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Reader;

trait ControllerAnnotationExtractorTrait
{
    abstract protected function getAnnotationReader(): Reader;

    /**
     * @param callable|null $controller
     * @param string        $annotationClass
     *
     * @return null|object
     */
    protected function getClassAnnotationFromController(callable $controller = null, string $annotationClass)
    {
        $objectToReflect = $this->getObjectToReflect($controller);

        if (is_object($objectToReflect)) {
            return $this->getAnnotationReader()->getClassAnnotation(
                new \ReflectionObject($objectToReflect),
                $annotationClass
            );
        }
    }

    /**
     * @param callable|null $controller
     * @param string        $annotationClass
     *
     * @return object|null
     */
    protected function getMethodAnnotationFromController(callable $controller = null, string $annotationClass)
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

    /**
     * @param callable|null $controller
     *
     * @return object|null
     */
    protected function getObjectToReflect(callable $controller = null)
    {
        if (is_object($controller)) {
            return $controller;
        }

        if (is_array($controller) && isset($controller[0]) && is_object($controller[0])) {
            return $controller[0];
        }
    }
}
