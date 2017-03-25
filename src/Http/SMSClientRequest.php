<?php

namespace Mediumart\Orange\SMS\Http;

use GuzzleHttp\Client;

abstract class SMSClientRequest
{
    /**
     * base api uri.
     */
    const BASE_URI = 'https://api.orange.com';

    /**
     * Describes the SSL certificate verification behavior of a request.
     */
    protected static $verify_ssl = true;

    /**
     * @var Client
     */
    protected static $httpClient;

    /**
     * Http request method.
     *
     * @return string
     */
    abstract public function method();

    /**
     * The uri for the current request.
     *
     * @return string
     */
    abstract public function uri();

    /**
     * Http request options.
     *
     * @return array
     */
    abstract public function options();

    /**
     * Set the SSL certificate verification behavior of a request.
     *
     * @param bool|string $value
     * @return $this
     */
    public static function verify($value)
    {
        if(is_bool($value) || is_string($value))
            static::$verify_ssl = $value;
    }

    /**
     * Set the http client.
     *
     * @param Client $client
     */
    public static function setHttpClient(Client $client)
    {
        static::$httpClient = $client;
    }

    /**
     * Execute the request.
     *
     * @param null $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    final public function execute($options = null)
    {
        $client = static::$httpClient ?: new Client([
            'verify' => static::$verify_ssl,
            'http_errors' => false
        ]);

        return $client->request(
            $this->method(),
            $this->uri(),
            $options ?: $this->options()
        );
    }
}