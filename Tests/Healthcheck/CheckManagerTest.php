<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck;

use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class CheckManagerTest extends HealthcheckKernelTestCase
{
    /**
     * @var CheckManager
     */
    protected $livenessManager;

    /**
     * @var CheckManager
     */
    protected $readinessManager;

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->livenessManager = null;
        $this->readinessManager = null;
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->livenessManager = static::$kernel->getContainer()->get('vdm_healthcheck.manager.liveness_manager');
        $this->readinessManager = static::$kernel->getContainer()->get('vdm_healthcheck.manager.readiness_manager');
    }

    /**
     * {@inheritDoc}
     */
    protected static function getAppName(): string
    {
        return 'app1';
    }

    public function testCheckOk()
    {
        $result = $this->livenessManager->check();
        $checkerResults = $result->getResults();

        $this->assertTrue($result->isUp());
        $this->assertTrue($checkerResults['always_true1']->isUp());
        $this->assertTrue($checkerResults['always_true2']->isUp());
        $this->assertTrue($checkerResults['always_true3']->isUp());
    }

    public function testCheckKo()
    {
        $result = $this->readinessManager->check();
        $checkerResults = $result->getResults();

        $this->assertFalse($result->isUp());
        $this->assertTrue($checkerResults['always_true1']->isUp());
        $this->assertFalse($checkerResults['always_false1']->isUp());
        $this->assertFalse($checkerResults['always_false2']->isUp());
    }
}
