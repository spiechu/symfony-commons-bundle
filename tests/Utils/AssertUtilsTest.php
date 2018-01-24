<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Utils;

use Spiechu\SymfonyCommonsBundle\Utils\AssertUtils;

class AssertUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testZeroIsNotEmptyString()
    {
        if (!AssertUtils::isNotEmpty('0')) {
            self::fail('0 is not considered empty');
        }
    }

    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /empty string/i
     */
    public function testEmptyStringWillThrowException()
    {
        \assert(AssertUtils::isNotEmpty(''), 'empty string');
    }
}
