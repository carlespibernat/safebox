<?php

namespace App\Model;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ApiTestCase extends TestCase
{
    const DEFAULT_API_BASE_URI = 'http://host.docker.internal';

    /** @var  string */
    private $apiBaseUri;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configureBaseUri();
    }

    /**
     * Performs a request to an api endpoint
     *
     * @param string $endpoint
     * @param string $method
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request(string $endpoint, string $method = 'GET', string $body = '')
    {
        $client = new Client([
            'base_uri' => $this->apiBaseUri,
            'http_errors' => false
        ]);

        return $client->request($method, $endpoint, [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Configures api base uri
     */
    protected function configureBaseUri()
    {
        $this->apiBaseUri = self::DEFAULT_API_BASE_URI;
    }
}