<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;

class CheckerConfigTest extends TestCase
{
    public function testGetters()
    {
        $checkerConfig = new CheckerConfig('name', 'type', ['arg1']);
        $this->assertEquals('name', $checkerConfig->getName());
        $this->assertEquals('type', $checkerConfig->getType());
        $this->assertEquals(['arg1'], $checkerConfig->getArguments());
    }

    public function testOptionalConstructorArgs()
    {
        $checkerConfig = new CheckerConfig('name', 'type');
        $this->assertEquals('name', $checkerConfig->getName());
        $this->assertEquals('type', $checkerConfig->getType());
        $this->assertEquals([], $checkerConfig->getArguments());
    }
}
