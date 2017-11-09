<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_1;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;

/**
 * @ApiVersion("1.1")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
}
