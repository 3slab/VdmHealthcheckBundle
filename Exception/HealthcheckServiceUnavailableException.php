<?php

namespace Vdm\Bundle\HealthcheckBundle\Exception;

use Throwable;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class HealthcheckServiceUnavailableException extends \Exception
{
    /**
     * @var string
     */
    protected $checkErrorType;

    /**
     * HealthcheckServiceUnavailableException constructor.
     *
     * @param AbstractChecker $checker
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        AbstractChecker $checker,
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->checkErrorType = $checker->getErrorType();

        $this->message = "Healthcheck failed [$this->checkErrorType]";
        if ($message) {
            $this->message .= " : $message";
        }
    }

    /**
     * Return the error type from the checker
     *
     * @return string
     */
    public function getCheckErrorType(): string
    {
        return $this->checkErrorType;
    }
}
