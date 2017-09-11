<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\EventListener;

use Spiechu\SymfonyCommonsBundle\EventListener\GetMethodOverrideListener;

class GetMethodOverrideListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $queryParamName = 'testparam';

    /**
     * @var string[]
     */
    protected $methodsToOverride = [
        'a', 'b', 'c',
    ];

    /**
     * @var GetMethodOverrideListener
     */
    protected $testedListener;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->testedListener = new GetMethodOverrideListener($this->queryParamName, $this->methodsToOverride);
    }

    public function testListenerWillRejectNonStringArguments()
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessageRegExp('/non string/i');

        new GetMethodOverrideListener($this->queryParamName, [5, '6']);
    }
}
