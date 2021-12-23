<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class AuthorizationRequest extends SMSClientRequest
{
    /**
     * @var string
     */
    private $clientID;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * AuthorizeClientRequest constructor.
     * @param $clientID
     * @param $clientSecret
     */
    public function __construct($clientID, $clientSecret)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Http request method.
     *
     * @return string
     */
    public function method()
    {
        return 'POST';
    }

    /**
     * The uri for the current request.
     *
     * @return string
     */
    public function uri()
    {
        return static::BASE_URI.'/oauth/v3/token';
    }

    /**
     * Http request options.
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => [
                'Authorization' => "Basic " . base64_encode("{$this->clientID}:{$this->clientSecret}")
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ];
    }
}
