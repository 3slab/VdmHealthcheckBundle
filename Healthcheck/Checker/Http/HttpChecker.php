<?php

namespace Vdm\Bundle\HealthcheckBundle\Healthcheck\Checker\Http;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\AbstractChecker;

class HttpChecker extends AbstractChecker
{
    public const DEFAULT_ERROR_TYPE = 'HTTP';
    public const REDIRECT_ERROR_TYPE = 'REDIRECT';
    public const TRANSPORT_ERROR_TYPE = 'TRANSPORT';

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $query;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @var int
     */
    protected $maxRedirects;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $errorType = self::DEFAULT_ERROR_TYPE;

    /**
     * DbalChecker constructor.
     * @param HttpClientInterface $client
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array $query
     * @param mixed $body
     * @param int $maxRedirects
     * @param int $timeout
     */
    public function __construct(
        HttpClientInterface $client,
        string $method,
        string $url,
        array $headers = [],
        array $query = [],
        $body = null,
        int $maxRedirects = 0,
        int $timeout = 5
    ) {
        $this->client = $client;
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->query = $query;
        $this->body = $body;
        $this->maxRedirects = $maxRedirects;
        $this->timeout = $timeout;
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
        try {
            $response = $this->client->request(
                $this->method,
                $this->url,
                [
                    'headers' => $this->headers,
                    'query' => $this->query,
                    'body' => $this->body,
                    'max_redirects' => $this->maxRedirects,
                    'timeout' => $this->timeout
                ]
            );
            $response->getStatusCode();
            $response->getContent(true);

            $isUp = true;
        } catch (ClientExceptionInterface $e) {
            $this->errorType = (string) $e->getResponse()->getStatusCode();
            $isUp = false;
        } catch (ServerExceptionInterface $e) {
            $this->errorType = (string) $e->getResponse()->getStatusCode();
            $isUp = false;
        } catch (RedirectionExceptionInterface $e) {
            $this->errorType = self::REDIRECT_ERROR_TYPE;
            $isUp = false;
        } catch (TransportExceptionInterface $e) {
            $this->errorType = self::TRANSPORT_ERROR_TYPE;
            $isUp = false;
        } catch (\Exception $e) {
            $this->errorType = self::DEFAULT_ERROR_TYPE;
            $isUp = false;
        }

        return $isUp;
    }
}
