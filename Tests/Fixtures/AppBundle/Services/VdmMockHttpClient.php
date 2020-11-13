<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Fixtures\AppBundle\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class VdmMockHttpClient implements HttpClientInterface
{
    /**
     * @var MockHttpClient
     */
    protected $mock;

    /**
     * VdmMockHttpClient constructor.
     */
    public function __construct()
    {
        $this->mock = $this->buildMockClient();
    }

    /**
     * @return MockHttpClient
     */
    protected function buildMockClient()
    {
        $callback = function ($method, $url, $options) {
            $content = 'response';
            $info = [];

            if ($url === 'http://vdmtest.local/success') {
                $info = ['http_code' => 204];
            } elseif ($url === 'http://vdmtest.local/error') {
                $info = ['http_code' => 500];
            } elseif ($url === 'http://vdmtest.local/redirectexception') {
                $info = ['http_code' => 305];
            } elseif ($url === 'http://vdmtest.local/clientexception') {
                $info = ['http_code' => 405];
            } elseif ($url === 'http://vdmtest.local/serverexception') {
                $info = ['http_code' => 505];
            } elseif ($url === 'http://vdmtest.local/transportexception') {
                $content = '';
                $info = ['http_code' => 200, 'error' => 'trigger transport error'];
            } else {
                $info = ['http_code' => 500];
            }

            return new MockResponse($content, $info);
        };

        return new MockHttpClient($callback);
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->mock->request($method, $url, $options);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        throw new \Exception('not used in unit tests');
    }
}
