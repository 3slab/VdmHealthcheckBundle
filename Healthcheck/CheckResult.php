<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

/**
 * Class CheckResult
 *
 * @package Vdm\Bundle\HealthcheckBundle\Healthcheck
 */
class CheckResult
{
    /**
     * Array of check results
     *
     * @var CheckerResult[]
     */
    public $results = [];

    /**
     * @param string $name
     * @param CheckerResult $result
     */
    public function addCheckerResult(string $name, CheckerResult $result)
    {
        $this->results[$name] = $result;
    }

    /**
     * Check if all the services are up
     *
     * @return bool
     */
    public function isUp(): bool
    {
        // Assume it is up if no checker result
        if (count($this->results) === 0) {
            return true;
        }

        return !in_array(false, array_map(function (CheckerResult $result) {
            return $result->isUp();
        }, $this->results));
    }

    /**
     * @return CheckerResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
