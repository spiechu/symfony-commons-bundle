<?php

$autoloader = require_once __DIR__.'/../vendor/autoload.php';

if (class_exists('\PHPUnit\Framework\TestCase') && !class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

return $autoloader;
