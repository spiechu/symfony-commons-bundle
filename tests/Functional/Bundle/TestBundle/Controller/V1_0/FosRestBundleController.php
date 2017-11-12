<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Model\TestJsonObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ApiVersion("1.0")
 */
class FosRestBundleController extends FOSRestController
{
    /**
     * @Rest\Route("/get-simple-view.{_format}", methods={"GET"})
     *
     * @Rest\View()
     */
    public function getRequestAction(Request $request): View
    {
        return $this->view(new TestJsonObject($request->query->get('property1'), '2'), 200);
    }
}
