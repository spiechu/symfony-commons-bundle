<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Service\ApiVersionFeaturesProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ApiVersion("1.0")
 */
class ApiVersionAnnotatedController extends Controller
{
    /**
     * @var ApiVersionFeaturesProvider
     */
    protected $apiVersionFeaturesProvider;

    /**
     * @param ApiVersionFeaturesProvider $apiVersionFeaturesProvider
     */
    public function __construct(ApiVersionFeaturesProvider $apiVersionFeaturesProvider)
    {
        $this->apiVersionFeaturesProvider = $apiVersionFeaturesProvider;
    }

    /**
     * @Route("/fancy-route")
     */
    public function fancyRouteAction()
    {
        return new Response('response from fancy route');
    }
}
