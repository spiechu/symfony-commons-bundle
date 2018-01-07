<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Utils;

use Spiechu\SymfonyCommonsBundle\Utils\StringUtils;

class StringUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testZeroIsNotEmptyString()
    {
        StringUtils::assertNotEmpty('0');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /empty string/i
     */
    public function testEmptyStringWillThrowException()
    {
        StringUtils::assertNotEmpty('');
    }
}
