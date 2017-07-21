<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Exception;
use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSDRCheckCallbackRequest extends SMSClientRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * CheckSMSDRCallbackRequest constructor.
     * @param $id
     * @throws \Exception
     */
    public function __construct($id)
    {
        if (! $id) {
            throw new Exception('Missing subscription id');
        }

        $this->id = $id;
    }

    /**
     * Http request method.
     *
     * @return string
     */
    public function method()
    {
        return 'GET';
    }

    /**
     * The uri for the current request.
     *
     * @return string
     */
    public function uri()
    {
        // '.urlencode($this->sender).'/
        return static::BASE_URI.'/smsmessaging/v1/outbound/subscriptions/'.$this->id;
    }

    /**
     * Http request options.
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => ['Content-Type' => 'application/json']
        ];
    }
}
