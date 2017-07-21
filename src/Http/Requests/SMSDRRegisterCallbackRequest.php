<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Exception;
use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSDRRegisterCallbackRequest extends SMSClientRequest
{
    /**
     * @var array
     */
    protected $body;
    /**
     * @var string
     */
    private $senderAddress;

    /**
     * RegisterSMSDRCallbackRequest constructor.
     *
     * @param $callbackUri
     * @param $senderAddress
     * @throws \Exception
     */
    public function __construct($callbackUri, $senderAddress)
    {
        $this->enforceHttpSecureProtocol($callbackUri);

        if (! $senderAddress) {
            throw new Exception('Missing sender address');
        }

        $this->senderAddress = 'tel:'.$this->normalizePhoneNumber($senderAddress);

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
        return static::BASE_URI.'/smsmessaging/v1/outbound/'.urlencode($this->senderAddress).'/subscriptions';
    }

    /**
     * Http request options.
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => ['Content-Type' => 'application/json'],
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
