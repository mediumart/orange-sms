<?php

namespace Tests;

use Mockery;
use Mediumart\Orange\SMS\SMS;
use Illuminate\Support\Facades\Cache;
use Mediumart\Orange\SMS\Http\SMSClient;
use Mediumart\Orange\SMS\OrangeSmsServiceProvider;

class OrangeSmsServiceProviderTest extends TestCase
{
    public function test_register_sms_instance()
    {
        $this->app->instance('orange-sms-client', $client = Mockery::mock(SMSClient::getInstance()));

        $this->assertInstanceOf(SMS::class, $this->app->make('orange-sms'));
        // test aliases.
        $this->assertInstanceOf(SMS::class, $this->app->make(SMS::class));
        $this->assertInstanceOf(SMSClient::class, $this->app->make(SMSClient::class));
    }

    public function test_cache_get_remember_client_token()
    {
        $provider = Mockery::mock(get_class($this->app->getProvider(OrangeSmsServiceProvider::class)).'[authorize]', [$this->app]);
        $provider->shouldReceive('authorize')->andReturn(['access_token' => $this->token]);

        $this->assertEquals($this->token, $provider->getClientToken());
        $this->assertTrue(Cache::has('orange.sms.token'));
        $this->assertEquals($this->token, Cache::get('orange.sms.token'));
    }

    public function test_get_client_token_invalid_credentials_exception()
    {
        $provider = Mockery::mock(get_class($this->app->getProvider(OrangeSmsServiceProvider::class)).'[authorize]', [$this->app]);
        $provider->shouldReceive('authorize')->andReturn(['error' => 'invalid_client', 'error_description' => 'error']);

        $this->expectException(\Mediumart\Orange\SMS\Exceptions\InvalidCredentialsException::class);
        $provider->getClientToken();
    }

    public function test_provided_services()
    {
        $provider = $this->app->getProvider(OrangeSmsServiceProvider::class);
        $this->assertEquals(['orange-sms', 'orange-sms-client', SMSClient::class, SMS::Class,], $provider->provides());
    }
}
