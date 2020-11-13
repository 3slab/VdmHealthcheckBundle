<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckServiceUnavailableException;

/**
 * Interface CheckerInterface
 *
 * @package Vdm\Bundle\HealthcheckBundle\Healthcheck
 */
abstract class AbstractChecker
{
    /**
     * Return the error type in case of an error during check
     *
     * @return string
     */
    abstract public function getErrorType(): string;

    /**
     * Launch check and returns true if up
     *
     * @throws HealthcheckServiceUnavailableException if not up
     * @return bool
     */
    abstract public function serviceCheck(): bool;

    /**
     * @return bool
     * @throws HealthcheckServiceUnavailableException
     */
    final public function check(): bool
    {
        try {
            $checkResult = $this->serviceCheck();
        } catch (\Exception $e) {
            throw new HealthcheckServiceUnavailableException(
                $this,
                "",
                0,
                $e
            );
        }

        if (!$checkResult) {
            throw new HealthcheckServiceUnavailableException($this);
        }

        return $checkResult;
    }
}
