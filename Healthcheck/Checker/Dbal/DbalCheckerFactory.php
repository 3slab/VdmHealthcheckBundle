<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Dbal;

use Doctrine\DBAL\Connection;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class DbalCheckerFactory extends AbstractCheckerFactory
{
    public const CHECKER_TYPE = 'dbal';

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return static::CHECKER_TYPE;
    }

    /**
     * {@inheritDoc}
     */
    protected function validate(CheckerConfig $config): void
    {
        if ($config->getType() !== self::CHECKER_TYPE) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                sprintf('invalid checker config type %s. supported type is %s', $config->getType(), self::CHECKER_TYPE)
            );
        }

        if (count($config->getArguments()) !== 1) {
            throw new HealthcheckFactoryInvalidConfigurationException('dbal checker only supports one argument');
        }

        if (!$config->getArguments()[0] instanceof Connection) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                'dbal checker argument is not an instance of Doctrine\DBAL\Connection'
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new DbalChecker($config->getArguments()[0]);
    }
}
