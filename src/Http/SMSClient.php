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
    protected static $instance;

    /**
     * SMSClient constructor.
     *
     * @throws \Error
     */
    protected function __construct()
    {
    }

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
    public function setTokenExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Get the expire_in in seconds
     *
     * @return string
     */
    public function getTokenExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Configure the instance.
     *
     * @param array $options
     * @return $this
     */
    public function configure()
    {
        switch (count($options = func_get_args())) {
            case 0:
                break;

            case 1:
                $this->configureInstance($options[0]);
                break;

            case 2:
                $this->configureInstanceAssoc(
                    static::authorize($options[0], $options[1])
                );
                break;

            default:
                throw new \InvalidArgumentException('invalid argument count');
                break;
        }

        return $this;
    }

    /**
     * Configure instance using options.
     *
     * @param  mixed  $options
     * @return $this
     */
    protected function configureInstance($options)
    {
        if (is_string($options)) {
            $this->setToken($options)->setTokenExpiresIn(null);
        } elseif (is_array($options)) {
            $this->configureInstanceAssoc($options);
        }
    }

    /**
     * Configure instance using assoc array options.
     *
     * @param  array  $options
     * @return $this
     */
    protected function configureInstanceAssoc(array $options)
    {
        if (array_key_exists('access_token', $options)) {
            $this->setToken($options['access_token']);
        }

        if (array_key_exists('expires_in', $options)) {
            $this->setTokenExpiresIn($options['expires_in']);
        }

        return $this;
    }

    /**
     * Execute a request against the Api server
     *
     * @param SMSClientRequest $request
     * @param bool $decodeJson
     * @return array
     */
    public function executeRequest(SMSClientRequest $request, $decodeJson = true)
    {
        $options = $request->options();

        if (! isset($options['headers']["Authorization"])) {
            $options['headers']["Authorization"] = "Bearer ". $this->getToken();
        }

        $response = $request->execute($options)->getBody();

        return $decodeJson ? json_decode($response, true) : $response;
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
        if (! static::$instance) {
            static::$instance = new static();
        }

        return static::$instance->configure(...func_get_args());
    }
}
