<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Exception;
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
     * @throws \Exception
     */
    public function __construct($message, $recipientNumber, $senderNumber, $senderName = null)
    {
        $this->throwsExceptionIfEmpty($recipientNumber, $senderNumber);

        $this->body = ['outboundSMSMessageRequest' => [
               'address' => 'tel:'.$this->normalizePhoneNumber($recipientNumber),
               'senderAddress' => $this->sender = 'tel:'.$this->normalizePhoneNumber($senderNumber),
               'outboundSMSTextMessage' => [ 'message' => $message ?: '']
           ]
        ];

        // if ($senderName) {
        //     $this->body['outboundSMSMessageRequest']['senderName'] = urlencode($senderName);
        // }
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

    /**
     * @param $recipientNumber
     * @param $senderNumber
     * @throws \Exception
     */
    private function throwsExceptionIfEmpty($recipientNumber, $senderNumber)
    {
        if (empty($senderNumber)) {
            throw new Exception('Missing Sender number');
        }

        if (empty($recipientNumber)) {
            throw new Exception('Missing Recipient number');
        }
    }
}
