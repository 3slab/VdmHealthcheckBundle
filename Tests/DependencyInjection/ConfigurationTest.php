<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Vdm\Bundle\HealthcheckBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    /**
     * @var Processor
     */
    protected $processor;

    public function setUp(): void
    {
        $this->processor = new Processor();
    }

    public function testEmptyConfig(): void
    {
        $configuration = new Configuration();
        $config = $this->processor->processConfiguration($configuration, []);

        $this->assertEquals(
            [
                'secret' => null,
                'liveness_path' => '/liveness',
                'readiness_path' => '/readiness',
                'liveness_checkers' => [],
                'readiness_checkers' => []
            ],
            $config
        );
    }

    public function testInvalidConfig(): void
    {
        $configuration = new Configuration();
        $config = $this->processor->processConfiguration(
            $configuration,
            [
                'vdm_healthcheck' => [
                    'readiness_path' => '/path',
                    'liveness_checkers' => true
                ]
            ]
        );

        $this->assertEquals(
            [
                'secret' => null,
                'liveness_path' => '/liveness',
                'readiness_path' => '/path',
                'liveness_checkers' => [],
                'readiness_checkers' => []
            ],
            $config
        );
    }

    public function testInvalidConfigWithException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configuration = new Configuration();
        $config = $this->processor->processConfiguration(
            $configuration,
            [
                'vdm_healthcheck' => [
                    'path' => true,
                    'checkers' => [
                        'test' => ['unknown' => 'value']
                    ]
                ]
            ]
        );
    }

    public function testValidConfig(): void
    {
        $unprocessedConfig = [
            'vdm_healthcheck' => [
                'secret' => 'mysecret',
                'liveness_path' => '/mycustomliveness',
                'readiness_path' => '/mycustompath',
                'liveness_checkers' => [
                    'check1' => [
                        'type' => 'orm',
                        'arguments' => ['service1', 'service2']
                    ]
                ],
                'readiness_checkers' => [
                    'check1' => [
                        'type' => 'orm',
                        'arguments' => ['service1', 'service2']
                    ]
                ]
            ]
        ];

        $configuration = new Configuration();
        $config = $this->processor->processConfiguration(
            $configuration,
            $unprocessedConfig
        );

        $this->assertEquals($unprocessedConfig['vdm_healthcheck'], $config);
    }
}
