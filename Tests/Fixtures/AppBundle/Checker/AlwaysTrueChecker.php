<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Checker;

use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class AlwaysTrueChecker extends AbstractChecker
{
    /**
     * @var mixed
     */
    protected $arg1;

    /**
     * @var mixed
     */
    protected $arg2;

    /**
     * AlwaysTrueChecker constructor.
     * @param mixed|null $arg1
     * @param mixed|null $arg2
     */
    public function __construct($arg1 = null, $arg2 = null)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    /**
     * @return mixed
     */
    public function getArg1()
    {
        return $this->arg1;
    }

    /**
     * @return mixed
     */
    public function getArg2()
    {
        return $this->arg2;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorType(): string
    {
        return "";
    }

    /**
     * {@inheritDoc}
     */
    public function serviceCheck(): bool
    {
        return true;
    }
}
