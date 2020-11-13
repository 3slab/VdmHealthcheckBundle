<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Checker;

use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class AlwaysFalseChecker extends AbstractChecker
{
    /**
     * @var string
     */
    protected $errorType;

    /**
     * AlwaysFalseChecker constructor.
     * @param string $errorType
     */
    public function __construct($errorType = 'ERR1')
    {
        $this->errorType = $errorType;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * {@inheritDoc}
     */
    public function serviceCheck(): bool
    {
        return false;
    }
}
