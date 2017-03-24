<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class OutboundSMSRequest extends SMSClientRequest
{
    /**
     * Request body
     *
     * @var array
     */
    protected $body;

    /**
     * Recipient number
     *
     * @var string
     */
    protected $sender;

    /**
     * OutboundSMSObjectRequest constructor.
     * @param $message
     * @param $recipientNumber
     * @param $senderNumber
     * @param $senderName
     */
    public function __construct($message, $recipientNumber, $senderNumber, $senderName = null)
    {
        $this->sender = $senderNumber;

        $this->body = ['outboundSMSMessageRequest' => [
               'address' => $recipientNumber ?: '',
               'senderAddress' => $senderNumber ?: '',
               'outboundSMSTextMessage' => [ 'message' => $message ?: '']
           ]
        ];

        if ($senderName) $this->body['outboundSMSMessageRequest']['senderName'] = urlencode($senderName);
    }

    /**
     * @inherit
     *
     * @return string
     */
    public function method()
    {
        return 'POST';
    }

    /**
     * @inherit
     *
     * @return string
     * @throws \Exception
     */
    public function uri()
    {
        if (! $this->sender ) throw new \Exception('URI Missing Sender number');

        return static::BASE_URI."/smsmessaging/v1/outbound/".urlencode($this->sender)."/requests";
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function options()
    {
        return [
            'headers' => ["Content-Type" => "Application/json"],
            'body' => json_encode($this->body)
        ];
    }
}