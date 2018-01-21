<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Annotation;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class OverwrittenResponseSchemaValidator extends ResponseSchemaValidator
{
}
