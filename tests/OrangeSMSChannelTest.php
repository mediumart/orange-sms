<?php

namespace Orange\SMS\Tests;

use Mockery;
use Mediumart\Orange\SMS\Http\SMSClient;
use Mediumart\Notifier\Contracts\Channels\Dispatcher;
use Mediumart\Orange\SMS\Laravel\Notification\OrangeSMSChannel;

class OrangeSMSChannelTest extends TestCase
{
    public function test_can_handle_notification()
    {
        $this->assertTrue(OrangeSMSChannel::canHandleNotification('orange'));
    }

    public function test_create_driver()
    {
        $this->app->instance('orange-sms-client', Mockery::mock(SMSClient::getInstance()));

        $this->assertInstanceOf(Dispatcher::class, OrangeSMSChannel::createDriver('orange'));
    }
}
