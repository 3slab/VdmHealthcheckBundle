<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\DependencyInjection\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vdm\Bundle\HealthcheckBundle\Controller\HealthController;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class HealthControllerAppDbalTest extends HealthcheckKernelTestCase
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
        return 'appdbal';
    }

    public function testLivenessWrongPath()
    {
        $client = static::createClient();

        $client->request('GET', '/liveness');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testReadinessWrongPath()
    {
        $client = static::createClient();

        $client->request('GET', '/readiness');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLivenessWithSecretInQuery()
    {
        $client = static::createClient();

        $client->request('GET', '/customlivenesspath', ['secret' => 'mycustomsecret']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'db' => ['up' => true, 'errorType' => null],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testReadinessWithSecretInQuery()
    {
        $client = static::createClient();

        $client->request('GET', '/customreadinesspath', ['secret' => 'mycustomsecret']);

        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'db' => ['up' => false, 'errorType' => 'DB'],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testLivenessWithSecretInHeader()
    {
        $client = static::createClient();

        $client->request('GET', '/customlivenesspath', [], [], ['HTTP_VDM_HEALTHCHECK_SECRET' => 'mycustomsecret']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'db' => ['up' => true, 'errorType' => null],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testReadinessWithSecretInHeader()
    {
        $client = static::createClient();

        $client->request('GET', '/customreadinesspath', [], [], ['HTTP_VDM_HEALTHCHECK_SECRET' => 'mycustomsecret']);

        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'db' => ['up' => false, 'errorType' => 'DB'],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testLivenessWithoutSecret()
    {
        $client = static::createClient();

        $client->request('GET', '/customlivenesspath');

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            null,
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testReadinessWithoutSecret()
    {
        $client = static::createClient();

        $client->request('GET', '/customreadinesspath');

        $this->assertEquals(503, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            '',
            json_decode($client->getResponse()->getContent(), true)
        );
    }
}
