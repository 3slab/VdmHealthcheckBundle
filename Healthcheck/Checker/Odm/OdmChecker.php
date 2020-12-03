<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Odm;

use Doctrine\ODM\MongoDB\Configuration;
use MongoDB\Client;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class OdmChecker extends AbstractChecker
{
    public const DEFAULT_ERROR_TYPE = 'ODM';

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * OdmChecker constructor.
     * @param Client $client
     *
     */
    public function __construct(Client $client, Configuration $configuration)
    {
        $this->client = $client;
        $this->configuration = $configuration;
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
        return $this->client->selectDatabase($this->configuration->getDefaultDB())->command(['ping' => 1]) !== false;
    }
}
