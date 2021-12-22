<?php

namespace Orange\SMS\Tests;

use Mediumart\Orange\SMS\Http\SMSClient;
use Mediumart\Orange\SMS\SMS;

class SMSTest extends TestCase
{
    /**
     * @var \Mediumart\Orange\SMS\SMS
     */
    private $SMS;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->SMS = new SMS(SMSClient::getInstance($this->token));
    }

    /**
     * @test
     */
    public function client_contracts_request()
    {
        $this->setupRequestContext('contracts');

        $this->assertSame(['partnerContracts' => []], $this->SMS->balance());
    }

    /**
     * @test
     */
    public function client_orders_history_request()
    {
        $this->setupRequestContext('ordersHistory');

        $this->assertSame(['purchaseOrders' => []], $this->SMS->ordersHistory());
    }

    /**
     * @test
     */
    public function client_statistics_request()
    {
        $this->setupRequestContext('statistics');

        $this->assertSame(['partnerStatistics' => []], $this->SMS->statistics());
    }

    /**
     * @test
     */
    public function client_outbound_sms_request()
    {
        $this->setupRequestContext('outboundSms');

        $response = $this->SMS->message('hello')
                              ->from('+123456789')
                              ->to('987654321')
                              ->send();

        $this->assertSame(['outboundSMSMessageRequest' => []], $response);
    }

    /**
     * @test
     */
    public function client_set_sms_dr_subscriptions_url()
    {
        $this->setupRequestContext('setDeliveryReceiptNotificationUrl');

        $this->assertSame(['deliveryReceiptSubscription' => []], $this->SMS->setDeliveryReceiptNotificationUrl(
            $this->callbackUri,
            $this->sender
        ));
    }

    /**
     * @test
     */
    public function client_check_sms_dr_subscriptions_url()
    {
        $this->setupRequestContext('checkDeliveryReceiptNotificationUrl');

        $this->assertSame(['deliveryReceiptSubscription' => []], $this->SMS->checkDeliveryReceiptNotificationUrl(
            $this->smsDrSubscriptionID,
            $this->sender
        ));
    }

    /**
     * @test
     */
    public function client_delete_sms_dr_subscriptions_url()
    {
        $this->setupRequestContext('deleteDeliveryReceiptNotificationUrl');

        $this->assertNull($this->SMS->deleteDeliveryReceiptNotificationUrl(
            $this->smsDrSubscriptionID,
            $this->sender
        ));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function client_set_sms_dr_subscriptions_url_not_secure_protocol_argument_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->SMS->setDeliveryReceiptNotificationUrl(
            $this->callbackUriNotSecured,
            $this->sender
        );
    }

    /** @test */
    public function it_fluently_set_sms_client() 
    {
        $this->assertInstanceOf(SMS::class, $this->SMS->setClient(SMSClient::getInstance()));
    }
}
