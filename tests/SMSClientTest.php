<?php

namespace Orange\SMS\Tests;

use Mediumart\Orange\SMS\Http\SMSClient;

class SMSClientTest extends TestCase
{
    /** 
     * @test
     */
    public function constructor_is_protected() 
    {
        $this->assertFalse(
            (new \ReflectionMethod(SMSClient::class, '__construct'))->isPublic()
        );
    }

    /**
     * @test
     */
    public function it_configure()
    {
        $client = SMSClient::getInstance();

        // using setters
        $client->setToken('token');
        $this->assertEquals('token', $client->getToken());

        $client->setTokenExpiresIn('12345');
        $this->assertEquals('12345', $client->getTokenExpiresIn());

        // using array
        $client->configure([
            'access_token' => 'new_token',
            'expires_in'   => '54321'
        ]);
        $this->assertEquals('new_token', $client->getToken());
        $this->assertEquals('54321', $client->getTokenExpiresIn());
    }

    /**
     * @test
     */
    public function configure_instance()
    {
        // using getInstance() with array
        $client = SMSClient::getInstance([
            'access_token' => 'access_token',
            'expires_in' => '7776000'
        ]);
        
        $this->assertEquals('access_token', $client->getToken());
        $this->assertEquals('7776000', $client->getTokenExpiresIn());


        // using getInstance()
        $client = SMSClient::getInstance('new_access_token');

        $this->assertEquals('new_access_token', $client->getToken());
        $this->assertNull($client->getTokenExpiresIn());
    }

    /**
     * @test
     */
    public function client_authorization_request()
    {
        $this->setupRequestContext('authorization');

        // using static authorize()
        $response = SMSClient::authorize($this->clientID, $this->clientSecret);
        $this->assertSame([
            "token_type" => "Bearer",
            "access_token" => "i6m2iIcY0SodWSe...L3ojAXXrH",
            "expires_in" => "7776000"
        ], $response);


        // using static getInstance()
        $smsClient = SMSClient::getInstance($this->clientID, $this->clientSecret);
        $this->assertEquals('i6m2iIcY0SodWSe...L3ojAXXrH', $smsClient->getToken());
        $this->assertEquals('7776000', $smsClient->getTokenExpiresIn());
    }

    /**
     * @test
     *
     */
    public function client_authorization_request_with_wrong_credentials()
    {
        $this->setupRequestContext('authorization');

        $this->expectException(\GuzzleHttp\Exception\ClientException::class);

        SMSClient::authorize('wrong_client_id', 'wrong_client_secret');
    }

    /** 
     * @test
     * 
     */
    public function configure_instance_with_invalid_arguments_count() 
    {
        $this->expectException(\InvalidArgumentException::class);

        SMSClient::getInstance('arg1', 'arg2', 'arg3');
    }
}
