<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Checker;

use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class AlwaysFalseCheckerFactory extends AbstractCheckerFactory
{
    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return 'always_false';
    }

    /**
     * {@inheritDoc}
     */
    public function validate(CheckerConfig $config): void
    {
        if (count($config->getArguments()) > 1) {
            throw new HealthcheckFactoryInvalidConfigurationException('always_false supports up to 1 argument');
        }

        if (count($config->getArguments()) === 1 && !is_string($config->getArguments()[0])) {
            throw new HealthcheckFactoryInvalidConfigurationException('Invalid argument type for always_false');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new AlwaysFalseChecker(...$config->getArguments());
    }
}
