<?php

namespace Mediumart\Orange\SMS\Http;

use Mediumart\Orange\SMS\Http\Requests\AuthorizationRequest;

class SMSClient
{
    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * Expires time.
     *
     * @var string
     */
    protected $expiresIn;

    /**
     * SMSCLient singleton instance.
     *
     * @var static
     */
    private static $instance;

    /**
     * SMSClient constructor.
     *
     * @throws \Error
     */
    private function __construct() {}

    /**
     * Prevent object cloning.
     *
     * @return void
     */
    public function __clone() {}

    /**
     * Set the access token.
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the access token.
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the expires_in in seconds
     *
     * @param $expiresIn
     * @return $this
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Get the expire_in in seconds
     *
     * @return string
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Configure the instance.
     *
     * @param array $options
     * @return $this
     */
    public function configure(array $options)
    {
        if(array_key_exists('access_token', $options))
            $this->setToken($options['access_token']);

        if(array_key_exists('expires_in', $options))
            $this->setExpiresIn($options['expires_in']);

        return $this;
    }

    /**
     * Execute a request against the Api server
     *
     * @param SMSClientRequest $request
     * @return array
     */
    public function executeRequest(SMSClientRequest $request)
    {
        $options = $request->options();

        if(! isset($options['headers']["Authorization"]))
            $options['headers']["Authorization"] = "Bearer ". $this->getToken();

        return json_decode($request->execute($options)->getBody(), true);
    }

    /**
     * Get the client access token
     *
     * @param $clientID
     * @param $clientSecret
     * @return array
     */
    public static function authorize($clientID, $clientSecret)
    {
        return json_decode(
            (new AuthorizationRequest($clientID, $clientSecret))->execute()->getBody(), true
        );
    }

    /**
     * Get the prepared singleton instance of the client.
     *
     * @return SMSClient
     */
    public static function getInstance()
    {
        if(!static::$instance)
        {
            static::$instance = new static();
        }

        $args = func_get_args();

        if(count($args) === 1)
        {
            $arg = $args[0];

            if(is_string($arg)) static::$instance->configure(['access_token' => $arg, 'expires_in' => null]);

            elseif(is_array($arg)) static::$instance->configure($arg);
        }

        elseif(count($args) > 1)
        {
            $response = static::authorize($args[0], $args[1]);

            static::$instance->configure($response);
        }

        return static::$instance;
    }
}