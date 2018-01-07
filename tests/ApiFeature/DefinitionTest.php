<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\ApiFeature;

use Spiechu\SymfonyCommonsBundle\ApiFeature\Definition;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /empty feature name/i
     */
    public function testEmptyNameIsNotAcceptable()
    {
        new Definition('', null, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /since.{1,}not numeric/i
     */
    public function testWrongSince()
    {
        new Definition('test-feature', 'wrong', null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /until.{1,}not numeric/i
     */
    public function testWringUntil()
    {
        new Definition('test-feature', null, 'wrong');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /no version constraints/i
     */
    public function testNoVersionConstraints()
    {
        new Definition('test-feature', null, null);
    }
}
