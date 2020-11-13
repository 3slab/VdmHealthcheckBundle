<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Dbal;

use Doctrine\DBAL\Connection;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class DbalChecker extends AbstractChecker
{
    public const DEFAULT_ERROR_TYPE = 'DB';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * DbalChecker constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorType(): string
    {
        return self::DEFAULT_ERROR_TYPE;
    }

    /**
     * {@inheritDoc}
     */
    public function serviceCheck(): bool
    {
        return $this->connection->ping() !== false;
    }
}
