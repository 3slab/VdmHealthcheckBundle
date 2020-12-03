<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Odm;

use Doctrine\ODM\MongoDB\Configuration;
use MongoDB\Client;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class OdmCheckerFactory extends AbstractCheckerFactory
{
    public const CHECKER_TYPE = 'odm';

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

        if (count($config->getArguments()) !== 2) {
            throw new HealthcheckFactoryInvalidConfigurationException('odm checker only supports one argument');
        }

        if (!$config->getArguments()[0] instanceof Client) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                'odm checker argument 1 is not an instance of MongoDB\Client'
            );
        }

        if (!$config->getArguments()[1] instanceof Configuration) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                'odm checker argument 2 is not an instance of Doctrine\ODM\MongoDB\Configuration'
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new OdmChecker($config->getArguments()[0], $config->getArguments()[1]);
    }
}
