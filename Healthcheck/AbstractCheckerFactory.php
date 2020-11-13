<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;

/**
 * Abstract class CheckerFactoryInterface
 *
 * @package Vdm\Bundle\HealthcheckBundle\Healthcheck
 */
abstract class AbstractCheckerFactory
{
    /**
     * @param CheckerConfig $config
     * @return AbstractChecker
     * @throws HealthcheckFactoryInvalidConfigurationException
     */
    final public function build(CheckerConfig $config): AbstractChecker
    {
        $this->validate($config);
        return $this->instantiate($config);
    }

    /**
     * @param CheckerConfig $config
     * @throws HealthcheckFactoryInvalidConfigurationException
     */
    abstract protected function validate(CheckerConfig $config): void;

    /**
     * Get the type of the checker built by this factory
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * instantiate the checker managed by this factory
     *
     * @param CheckerConfig $config
     * @return AbstractChecker
     */
    abstract protected function instantiate(CheckerConfig $config): AbstractChecker;
}
