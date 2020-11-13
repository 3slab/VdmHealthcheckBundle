<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Http;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\HealthcheckBundle\Exception\HealthcheckFactoryInvalidConfigurationException;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerConfig;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractCheckerFactory;

class HttpCheckerFactory extends AbstractCheckerFactory
{
    public const CHECKER_TYPE = 'http';

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * HttpCheckerFactory constructor.
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public static function getType(): string
    {
        return static::CHECKER_TYPE;
    }

    /**
     * {@inheritDoc}
     */
    protected function validate(CheckerConfig $config): void
    {
        if ($config->getType() !== self::CHECKER_TYPE) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                sprintf('invalid checker config type %s. supported type is %s', $config->getType(), self::CHECKER_TYPE)
            );
        }

        if (count($config->getArguments()) < 2) {
            throw new HealthcheckFactoryInvalidConfigurationException('http checker needs at least 2 arguments');
        }

        if (count($config->getArguments()) > 7) {
            throw new HealthcheckFactoryInvalidConfigurationException(
                'http checker does not support more than 7 arguments'
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function instantiate(CheckerConfig $config): AbstractChecker
    {
        return new HttpChecker($this->client, ...$config->getArguments());
    }
}
