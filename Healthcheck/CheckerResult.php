<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck;

class CheckerResult
{
    /**
     * @var bool
     */
    protected $isUp;

    /**
     * @var string|null
     */
    protected $errorType;

    /**
     * CheckerConfig constructor.
     * @param bool $isUp
     * @param string|null $errorType
     */
    public function __construct(bool $isUp, ?string $errorType = null)
    {
        $this->isUp = $isUp;
        $this->errorType = $errorType;
    }

    /**
     * @return bool
     */
    public function isUp(): bool
    {
        return $this->isUp;
    }

    /**
     * @return string|null
     */
    public function getErrorType(): ?string
    {
        return $this->errorType;
    }
}
