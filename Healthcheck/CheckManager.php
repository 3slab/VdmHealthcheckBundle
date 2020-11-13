<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckServiceUnavailableException;

class CheckManager
{
    /**
     * @var AbstractChecker[]
     */
    protected $checks = [];

    /**
     * @param string $name
     * @param AbstractChecker $check
     * @return CheckManager
     */
    public function addCheck(string $name, AbstractChecker $check)
    {
        $this->checks[$name] = $check;

        return $this;
    }

    /**
     * @return AbstractChecker[]
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * @return CheckResult
     */
    public function check(): CheckResult
    {
        $result = new CheckResult();
        foreach ($this->checks as $name => $checker) {
            $checkerResult = $this->executeChecker($checker);
            $result->addCheckerResult($name, $checkerResult);
        }
        return $result;
    }

    /**
     * @param AbstractChecker $checker
     * @return CheckerResult
     */
    protected function executeChecker(AbstractChecker $checker): CheckerResult
    {
        try {
            $isUp = $checker->check();
            $errorType = null;
        } catch (HealthcheckServiceUnavailableException $e) {
            $isUp = false;
            $errorType = $e->getCheckErrorType();
        }

        return new CheckerResult($isUp, $errorType);
    }
}
