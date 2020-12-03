<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Odm;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Odm\OdmChecker;

class OdmCheckerTest extends TestCase
{
    public function testGetErrorType()
    {
        /** @var \MongoDB\Client $client */
        $client = $this
            ->getMockBuilder('MongoDB\Client')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Doctrine\ODM\MongoDB\Configuration $configuration */
        $configuration = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $odmChecker = new OdmChecker($client, $configuration);

        $this->assertEquals(OdmChecker::DEFAULT_ERROR_TYPE, $odmChecker->getErrorType());
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
        /** @var \MongoDB\Database $mockDatabase */
        $mockDatabase = $this
            ->getMockBuilder('MongoDB\Database')
            ->setMethods(['command'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockDatabase
            ->expects($this->once())
            ->method('command')
            ->will($this->returnValue($pingReturn));

        /** @var \MongoDB\Client $mockClient */
        $mockClient = $this
            ->getMockBuilder('MongoDB\Client')
            ->setMethods(['selectDatabase'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient
            ->expects($this->once())
            ->method('selectDatabase')
            ->will($this->returnValue($mockDatabase));

        /** @var \Doctrine\ODM\MongoDB\Configuration $mockConfiguration */
        $mockConfiguration = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Configuration')
            ->setMethods(['getDefaultDB'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockConfiguration
            ->expects($this->once())
            ->method('getDefaultDB');

        $odmChecker = new OdmChecker($mockClient, $mockConfiguration);

        $this->assertEquals($expectedValue, $odmChecker->serviceCheck());
    }
}
