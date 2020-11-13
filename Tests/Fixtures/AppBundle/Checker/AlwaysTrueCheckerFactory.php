<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Checker;

use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class AlwaysTrueCheckerFactory extends AbstractCheckerFactory
{
    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'always_true';
    }

    /**
     * {@inheritDoc}
     */
    public function validate(CheckerConfig $config): void
    {
        if (count($config->getArguments()) > 2) {
            throw new HealthcheckFactoryInvalidConfigurationException('always_true supports up to 2 argument');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new AlwaysTrueChecker(...$config->getArguments());
    }
}
