<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Model\TestJsonObject;
use Symfony\Component\HttpFoundation\Request;

class FosRestBundleVersionlessController extends FOSRestController
{
    /**
     * @Rest\Route("/get-simple-view-without-version.{_format}", methods={"GET"})
     *
     * @return View
     */
    public function getRequestAction(Request $request): View
    {
        return $this->view(new TestJsonObject($request->query->get('property1'), '2'), 200);
    }
}
