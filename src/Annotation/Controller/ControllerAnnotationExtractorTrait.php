<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Reader;

trait ControllerAnnotationExtractorTrait
{
    abstract protected function getAnnotationReader(): Reader;

    /**
     * @param null|callable $controller
     * @param string        $annotationClass
     *
     * @return null|object
     */
    protected function getClassAnnotationFromController(?callable $controller, string $annotationClass)
    {
        $objectToReflect = $this->getObjectToReflect($controller);

        if (\is_object($objectToReflect)) {
            return $this->getAnnotationReader()->getClassAnnotation(
                new \ReflectionObject($objectToReflect),
                $annotationClass
            );
        }
    }

    /**
     * @param null|callable $controller
     * @param string        $annotationClass
     *
     * @return null|object
     */
    protected function getMethodAnnotationFromController(?callable $controller, string $annotationClass)
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
    }

    /**
     * @param null|callable $controller
     *
     * @return null|object
     */
    protected function getObjectToReflect(?callable $controller)
    {
        if (\is_object($controller)) {
            return $controller;
        }

        if (\is_array($controller) && isset($controller[0]) && \is_object($controller[0])) {
            return $controller[0];
        }
    }
}
