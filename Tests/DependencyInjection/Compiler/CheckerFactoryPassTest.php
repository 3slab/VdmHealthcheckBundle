<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;
use Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Checker\AlwaysTrueChecker;
use Vdm\Bundle\HealthcheckBundle\Tests\HealthcheckKernelTestCase;

class CheckerFactoryPassTest extends HealthcheckKernelTestCase
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

    public function testMultipleLivenessCheckerBuilt()
    {
        $checkers = $this->livenessManager->getChecks();

        $this->assertCount(
            3,
            $checkers
        );

        $this->assertEquals(
            ['always_true1', 'always_true2', 'always_true3'],
            array_keys($checkers)
        );
    }

    public function testCompilerPassReplaceParametersAndServicesForLiveness()
    {
        $checkers = $this->livenessManager->getChecks();

        /** @var AlwaysTrueChecker $alwaysTrueChecker */
        $alwaysTrueChecker = $checkers['always_true1'];

        $this->assertEquals(
            true,
            $alwaysTrueChecker->getArg1()
        );

        $this->assertInstanceOf(
            ServiceLocator::class,
            $alwaysTrueChecker->getArg2()
        );
    }

    public function testMultipleReadinessCheckerBuilt()
    {
        $checkers = $this->readinessManager->getChecks();

        $this->assertCount(
            3,
            $checkers
        );

        $this->assertEquals(
            ['always_true1', 'always_false1', 'always_false2'],
            array_keys($checkers)
        );
    }

    public function testCompilerPassReplaceParametersAndServicesForReadiness()
    {
        $checkers = $this->readinessManager->getChecks();

        /** @var AlwaysTrueChecker $alwaysTrueChecker */
        $alwaysTrueChecker = $checkers['always_true1'];

        $this->assertEquals(
            'en',
            $alwaysTrueChecker->getArg1()
        );

        $this->assertNull(
            $alwaysTrueChecker->getArg2()
        );
    }
}
