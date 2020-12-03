<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Odm;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Odm\OdmChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Odm\OdmCheckerFactory;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;

class OdmCheckerFactoryTest extends TestCase
{
    public function testGetType()
    {
        $this->assertEquals(
            OdmCheckerFactory::CHECKER_TYPE,
            OdmCheckerFactory::getType()
        );
    }

    public function testValidateOk()
    {
        $this->expectNotToPerformAssertions();

        $client = $this
            ->getMockBuilder('MongoDB\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('odm1', OdmCheckerFactory::getType(), [$client, $configuration]);
        $checkerFactory = new OdmCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateNotEnoughArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('odm1', OdmCheckerFactory::getType());
        $checkerFactory = new OdmCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateTooMuchArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('odm1', OdmCheckerFactory::getType(), ['arg1', 'arg2', 'arg3']);
        $checkerFactory = new OdmCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateUnsupportedType()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('odm1', 'wrong_type', [$connection]);
        $checkerFactory = new OdmCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testBuild()
    {
        $client = $this
            ->getMockBuilder('MongoDB\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('odm1', OdmCheckerFactory::getType(), [$client, $configuration]);
        $checkerFactory = new OdmCheckerFactory();
        $result = $checkerFactory->build($checkerConfig);

        $this->assertInstanceOf(OdmChecker::class, $result);
    }

    public function testValidateWrongArgumentType()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('odm1', 'wrong_type', ['wrong_type1', 'wrong_type2']);
        $checkerFactory = new OdmCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }
}
