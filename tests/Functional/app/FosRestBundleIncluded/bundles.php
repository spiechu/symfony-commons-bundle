<?php

use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Spiechu\SymfonyCommonsBundle\SpiechuSymfonyCommonsBundle;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\TestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

return [
    new FrameworkBundle(),
    new SpiechuSymfonyCommonsBundle(),
    new SensioFrameworkExtraBundle(),
    new TestBundle(),
    new FOSRestBundle(),
    new JMSSerializerBundle(),
];
