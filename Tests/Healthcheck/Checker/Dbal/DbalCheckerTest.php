<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Dbal;

use Doctrine\DBAL\Connection;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Dbal\DbalChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class DbalCheckerTest extends HealthcheckKernelTestCase
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

        static::$kernel->getContainer()
            ->get('doctrine')
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(null);
    }

    /**
     * {@inheritDoc}
     */
    protected static function getAppName(): string
    {
        return 'appdbal';
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
        /** @var Connection $mockConnection */
        $mockConnection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $dbalChecker = new DbalChecker($mockConnection);

        $this->assertEquals(DbalChecker::DEFAULT_ERROR_TYPE, $dbalChecker->getErrorType());
    }

    /**
     * @testWith [true, true]
     *           [false, false]
     *
     * @param bool $expectedValue
     * @param bool $pingReturn
     *
     * @throws \Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckServiceUnavailableException
     */
    public function testCheckService(bool $expectedValue, bool $pingReturn)
    {
        $mockPlatform = $this
            ->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')
            ->disableOriginalConstructor()
            ->getMock();

        $mockConnection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        if (method_exists(Connection::class, 'ping')) {
            $mockConnection
                ->expects($this->once())
                ->method('ping')
                ->will($this->returnValue($pingReturn));
        } else {
            $mockPlatform
                ->expects($this->once())
                ->method('getDummySelectSQL')
                ->will($this->returnValue('SELECT 1=1'));

            $executeQueryMethod = $mockConnection
                ->expects($this->once())
                ->method('executeQuery');

            if (!$pingReturn) {
                $executeQueryMethod->willThrowException(new \Exception());
            }

            $mockConnection
                ->expects($this->once())
                ->method('getDatabasePlatform')
                ->will($this->returnValue($mockPlatform));
        }

        /** @var Connection $mockConnection */
        $dbalChecker = new DbalChecker($mockConnection);

        $this->assertEquals($expectedValue, $dbalChecker->serviceCheck());
    }
}
