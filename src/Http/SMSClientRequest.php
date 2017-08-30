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
    public function options()
    {
        return [];
    }

    /**
     * Set the SSL certificate verification behavior of a request.
     *
     * @param bool|string $value
     * @return $this
     */
    public static function verify($value)
    {
        if (is_bool($value) || is_string($value)) {
            static::$verify_ssl = $value;
        }
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
     * Get the http client.
     *
     * @return \GuzzleHttp\Client
     */
    public static function getHttpClient()
    {
        if (static::$httpClient &&
            static::$httpClient->getConfig('verify') === static::$verify_ssl
            ) {
            return static::$httpClient;
        }

        return new Client(['http_errors' => false, 'verify' => static::$verify_ssl]);
    }

    /**
     * Execute the request.
     *
     * @param array|null $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    final public function execute($options = null)
    {
        return $this
            ->getHttpClient()
            ->request(
                $this->method(), 
                $this->uri(), 
                $options ?: $this->options()
            );
    }

    /**
     * Normalize phone number.
     *
     * @param  $phone
     * @return string
     */
    protected function normalizePhoneNumber($phone)
    {
        $phone = (string) $phone;

        if (substr($phone, 0, 1) !== '+') {
            return '+' . $phone;
        }

        return $phone;
    }
}
