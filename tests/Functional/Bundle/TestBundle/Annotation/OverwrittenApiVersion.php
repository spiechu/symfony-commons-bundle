<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Annotation;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;

/**
 * @Annotation
 * @Target("CLASS")
 */
class OverwrittenApiVersion extends ApiVersion
{
}
