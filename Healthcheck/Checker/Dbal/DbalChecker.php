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
        // ping method removed in doctrine 3.0
        if (method_exists($this->connection, 'ping')) {
            return $this->connection->ping() !== false;
        }

        return $this->pingConnection();
    }

    /**
     * Implement ping method of doctrine connection as it has been removed in 3.0
     *
     * @return bool
     */
    protected function pingConnection(): bool
    {
        try {
            $this->connection->executeQuery($this->connection->getDatabasePlatform()->getDummySelectSQL());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
