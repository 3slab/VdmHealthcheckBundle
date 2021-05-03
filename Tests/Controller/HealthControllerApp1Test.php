<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Controller;

use Vdm\Bundle\HealthcheckBundle\Controller\HealthController;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class HealthControllerApp1Test extends HealthcheckKernelTestCase
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

    public function testLiveness()
    {
        $client = static::createClient();

        $client->request('GET', '/liveness');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'always_true1' => ['up' => true, 'errorType' => null],
                'always_true2' => ['up' => true, 'errorType' => null],
                'always_true3' => ['up' => true, 'errorType' => null],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testReadiness()
    {
        $client = static::createClient();

        $client->request('GET', '/readiness');

        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'always_false1' => ['up' => false, 'errorType' => 'BDD_DOWN'],
                'always_false2' => ['up' => false, 'errorType' => 'UNKNOWN_SERVICE'],
                'always_true1' => ['up' => true, 'errorType' => null],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }
}
