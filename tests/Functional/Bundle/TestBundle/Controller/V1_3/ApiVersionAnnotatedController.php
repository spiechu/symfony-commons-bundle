<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_3;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_2\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;

/**
 * @ApiVersion("1.3")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
}
