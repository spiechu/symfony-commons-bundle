<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_2;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_1\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ApiVersion("1.2")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
    /**
     * @Route("/features-route")
     *
     * @return JsonResponse
     */
    public function featuresRouteAction(): JsonResponse
    {
        return new JsonResponse(array_values(array_map(
            'strval',
            $this->apiVersionFeaturesProvider->getAvailableFeatures()
        )));
    }

    /**
     * @Route("/json-serialization-features-route")
     *
     * @return JsonResponse
     */
    public function jsonSerializationFeaturesRouteAction(): JsonResponse
    {
        return new JsonResponse(
            json_encode(array_values($this->apiVersionFeaturesProvider->getAvailableFeatures())),
            200,
            [],
            true
        );
    }
}
