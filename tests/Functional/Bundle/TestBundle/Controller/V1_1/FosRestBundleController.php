<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_1;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0\FosRestBundleController as BaseFosRestBundleController;

/**
 * @ApiVersion("1.1")
 */
class FosRestBundleController extends BaseFosRestBundleController
{
}
