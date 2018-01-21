<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_1;

use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Annotation\OverwrittenApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;

/**
 * @OverwrittenApiVersion("1.1")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
}
