<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck\Checker\Http;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Http\HttpChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Http\HttpCheckerFactory;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;

class HttpCheckerFactoryTest extends TestCase
{
    public function testGetType()
    {
        $this->assertEquals(
            HttpCheckerFactory::CHECKER_TYPE,
            HttpCheckerFactory::getType()
        );
    }

    public function testValidateOk()
    {
        $this->expectNotToPerformAssertions();

        /** @var HttpClientInterface $client */
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('http1', HttpCheckerFactory::getType(), ['GET', 'http://host/first']);
        $checkerFactory = new HttpCheckerFactory($client);
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateNotEnoughArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        /** @var HttpClientInterface $client */
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('http1', HttpCheckerFactory::getType(), ['GET']);
        $checkerFactory = new HttpCheckerFactory($client);
        $checkerFactory->build($checkerConfig);
    }

    public function testValidateTooMuchArgument()
    {
        $this->expectException(HealthcheckFactoryInvalidConfigurationException::class);

        /** @var HttpClientInterface $client */
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig(
            'db1',
            HttpCheckerFactory::getType(),
            ['arg1', 'arg2', 'arg3', 'arg4', 'arg5', 'arg6', 'arg7', 'arg8']
        );
        $checkerFactory = new HttpCheckerFactory($client);
        $checkerFactory->build($checkerConfig);
    }

    public function testBuild()
    {
        /** @var HttpClientInterface $client */
        $client = $this
            ->getMockBuilder('Symfony\Contracts\HttpClient\HttpClientInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $checkerConfig = new CheckerConfig('db1', HttpCheckerFactory::getType(), ['GET', 'http://host/uri']);
        $checkerFactory = new HttpCheckerFactory($client);
        $result = $checkerFactory->build($checkerConfig);

        $this->assertInstanceOf(HttpChecker::class, $result);
    }
}
