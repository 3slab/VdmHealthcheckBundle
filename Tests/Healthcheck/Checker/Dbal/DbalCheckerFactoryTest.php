<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Dbal;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Dbal\DbalChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Dbal\DbalCheckerFactory;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;

class DbalCheckerFactoryTest extends TestCase
{
    public function testGetType()
    {
        $this->assertEquals(
            DbalCheckerFactory::CHECKER_TYPE,
            DbalCheckerFactory::getType()
        );
    }

    public function testValidateOk()
    {
        $this->expectNotToPerformAssertions();

        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('db1', DbalCheckerFactory::getType(), [$connection]);
        $checkerFactory = new DbalCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateNotEnoughArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('db1', DbalCheckerFactory::getType());
        $checkerFactory = new DbalCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateTooMuchArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('db1', DbalCheckerFactory::getType(), ['arg1', 'arg2']);
        $checkerFactory = new DbalCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateUnsupportedType()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('db1', 'wrong_type', [$connection]);
        $checkerFactory = new DbalCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }

    public function testBuild()
    {
        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('db1', DbalCheckerFactory::getType(), [$connection]);
        $checkerFactory = new DbalCheckerFactory();
        $result = $checkerFactory->build($checkerConfig);

        $this->assertInstanceOf(DbalChecker::class, $result);
    }

    public function testValidateWrongArgumentType()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        $checkerConfig = new CheckerConfig('db1', 'wrong_type', ['wrong_type']);
        $checkerFactory = new DbalCheckerFactory();
        $checkerFactory->build($checkerConfig);
    }
}
