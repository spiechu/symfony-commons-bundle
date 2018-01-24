<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\ApiFeature;

use Spiechu\SymfonyCommonsBundle\ApiFeature\Definition;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /empty feature name/i
     */
    public function testEmptyNameIsNotAcceptable()
    {
        Definition::create('', null, null);
    }

    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /since.{1,}not numeric/i
     */
    public function testWrongSince()
    {
        Definition::create('test-feature', 'wrong', null);
    }

    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /until.{1,}not numeric/i
     */
    public function testWringUntil()
    {
        Definition::create('test-feature', null, 'wrong');
    }

    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /no version constraints/i
     */
    public function testNoVersionConstraints()
    {
        Definition::create('test-feature', null, null);
    }

    /**
     * @expectedException \AssertionError
     * @expectedExceptionMessageRegExp /until parameter is lower than since parameter/i
     */
    public function testUntilIsLowerThanSinceVersionConstraint()
    {
        Definition::create('test-feature', '1.2', '1.1');
    }

    public function testDefinitionIsCorrectlySet()
    {
        $definition = Definition::create('correct-definition', '1.2', '1.3');

        self::assertSame($definition->getName(), 'correct-definition');
        self::assertSame($definition->getSince(), '1.2');
        self::assertSame($definition->getUntil(), '1.3');
    }
}
