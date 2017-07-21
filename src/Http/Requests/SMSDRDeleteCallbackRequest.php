<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Exception;
use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSDRDeleteCallbackRequest extends SMSClientRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $sender;

    /**
     * SMSDRDeleteCallbackRequest constructor.
     * @param $id
     * @param $sender
     * @throws \Exception
     */
    public function __construct($id, $sender)
    {
        if (! $sender) {
            throw new Exception('Missing sender address');
        }

        if (! $id) {
            throw new Exception('Missing subscription id');
        }

        $this->sender = 'tel:'.$this->normalizePhoneNumber($sender);

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
        return static::BASE_URI.'/smsmessaging/v1/outbound/'.urlencode($this->sender).'/subscriptions/'.$this->id;
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
