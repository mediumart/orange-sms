<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSDRDeleteCallbackRequest extends SMSClientRequest
{
    /**
     * @var
     */
    private $id;

    /**
     * SMSDRDeleteCallbackRequest constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Http request method.
     *
     * @return string
     */
    public function method()
    {
        return 'DELETE';
    }

    /**
     * The uri for the current request.
     *
     * @return string
     */
    public function uri()
    {
        // not very sure about this one. never tested yet| confusing documentation..
        return static::BASE_URI.'/smsmessaging/v1/outbound/tel%3A%2B400/subscriptions/'.$this->id;
    }

    /**
     * Http request options.
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => ['Content-Type: application/json']
        ];
    }
}