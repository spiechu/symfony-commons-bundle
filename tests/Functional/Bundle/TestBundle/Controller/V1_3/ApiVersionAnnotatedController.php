<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_3;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Service\ApiVersionFeaturesProvider;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_2\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ApiVersion("1.3")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
    /**
     * @Route(
     *   "/add-feature/{name}/since/{since}/until/{until}",
     *   methods={"POST"},
     *   defaults={"until" : null},
     *   requirements={
     *     "name" : "[a-z\-]{1,}",
     *     "since" : "[0-9]{1,}(?:\.[0-9]{1,}){0,1}",
     *     "until" : "(?:[0-9]{1,}(?:\.[0-9]{1,}){0,1}){0,1}"
     *   }
     * )
     *
     * @param ApiVersionFeaturesProvider $apiVersionFeaturesProvider
     * @param string                     $name
     * @param string                     $since
     * @param null|string                $until
     *
     * @return JsonResponse
     */
    public function addFeatureDynamicallyAction(ApiVersionFeaturesProvider $apiVersionFeaturesProvider, string $name, string $since, ?string $until): JsonResponse
    {
        $apiVersionFeaturesProvider->addFeature($name, $since, $until);

        return new JsonResponse([
            'added-feature' => $apiVersionFeaturesProvider->getFeature($name),
            'all-known-features' => array_values($apiVersionFeaturesProvider->getAllKnownFeatures()),
            'available-features' => array_values($apiVersionFeaturesProvider->getAvailableFeatures()),
        ]);
    }
}
