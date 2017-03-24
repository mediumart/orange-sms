<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSDRRegisterCallbackRequest extends SMSClientRequest
{
    /**
     * @var array
     */
    protected $body;

    /**
     * RegisterSMSDRCallbackRequest constructor.
     *
     * @param $callbackUri
     */
    public function __construct($callbackUri)
    {
        $this->enforceHttpSecureProtocol($callbackUri);

        $this->body = [
            "deliveryReceiptSubscription" => [
                "callbackReference" => [
                    "notifyURL" => $callbackUri
                ]
            ]
        ];
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
        // not very sure about this one. never tested yet| confusing documentation..
        return static::BASE_URI.'/smsmessaging/v1/outbound/tel%3A%2B400/subscriptions';
    }

    /**
     * Http request options.
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => ['Content-Type: application/json'],
            'body' => json_encode($this->body)
        ];
    }

    /**
     * @param $callbackUri
     */
    protected function enforceHttpSecureProtocol($callbackUri)
    {
        if (substr($callbackUri, 0, strlen('https://')) !== 'https://') {
            throw new \InvalidArgumentException(
                "Url callback protocol must be secured and starts with: 'https://'"
            );
        }
    }
}
