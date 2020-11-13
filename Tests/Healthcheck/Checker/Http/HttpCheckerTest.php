<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Http;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Http\HttpChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Services\VdmMockHttpClient;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class HttpCheckerTest extends HealthcheckKernelTestCase
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
        return 'apphttp';
    }

    public function testCheckOk()
    {
        $checkResult = $this->livenessManager->check();

        $this->assertTrue($checkResult->isUp());
    }

    public function testCheckKo()
    {
        $checkResult = $this->readinessManager->check();

        $this->assertFalse($checkResult->isUp());
    }

    public function testGetErrorType()
    {
        /** @var HttpClientInterface $client */
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $httpChecker = new HttpChecker($client, 'GET', 'http://local/uri');

        $this->assertEquals(HttpChecker::DEFAULT_ERROR_TYPE, $httpChecker->getErrorType());
    }

    public function testServiceCheckOk()
    {
        /** @var HttpClientInterface $client */
        $client = new VdmMockHttpClient();

        $httpChecker = new HttpChecker($client, 'GET', 'http://vdmtest.local/success');

        $this->assertTrue($httpChecker->serviceCheck());
    }

    public function testServiceCheckRedirectException()
    {
        /** @var HttpClientInterface $client */
        $client = new VdmMockHttpClient();

        $httpChecker = new HttpChecker($client, 'GET', 'http://vdmtest.local/redirectexception');

        $this->assertFalse($httpChecker->serviceCheck());
        $this->assertEquals(HttpChecker::REDIRECT_ERROR_TYPE, $httpChecker->getErrorType());
    }

    public function testServiceCheckClientException()
    {
        /** @var HttpClientInterface $client */
        $client = new VdmMockHttpClient();

        $httpChecker = new HttpChecker($client, 'GET', 'http://vdmtest.local/clientexception');

        $this->assertFalse($httpChecker->serviceCheck());
        $this->assertEquals(405, $httpChecker->getErrorType());
    }

    public function testServiceCheckServerException()
    {
        /** @var HttpClientInterface $client */
        $client = new VdmMockHttpClient();

        $httpChecker = new HttpChecker($client, 'GET', 'http://vdmtest.local/serverexception');

        $this->assertFalse($httpChecker->serviceCheck());
        $this->assertEquals(505, $httpChecker->getErrorType());
    }

    public function testServiceCheckTransportException()
    {
        /** @var HttpClientInterface $client */
        $client = new VdmMockHttpClient();

        $httpChecker = new HttpChecker($client, 'GET', 'http://vdmtest.local/transportexception');

        $this->assertFalse($httpChecker->serviceCheck());
        $this->assertEquals(HttpChecker::TRANSPORT_ERROR_TYPE, $httpChecker->getErrorType());
    }

    public function testHttpClientCallArguments()
    {
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $client
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo('http://vdmtest.local/myurl'),
                $this->equalTo(
                    [
                        'headers' => ['Content-Type' => 'text/plain'],
                        'query' => ['param1' => 'value1'],
                        'body' => 'content',
                        'max_redirects' => 5,
                        'timeout' => 10
                    ]
                )
            )
            ->will($this->returnValue(new MockResponse('', ['http_code' => 204])));

        $httpChecker = new HttpChecker(
            $client,
            'GET',
            'http://vdmtest.local/myurl',
            ['Content-Type' => 'text/plain'],
            ['param1' => 'value1'],
            'content',
            5,
            10
        );
        $httpChecker->serviceCheck();
    }
}
