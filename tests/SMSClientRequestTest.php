<?php

namespace Orange\SMS\Tests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class SMSClientRequestTest extends TestCase
{
    /** @test */
    public function it_set_http_client()
    {
        SMSClientRequest::setHttpClient($http = new \GuzzleHttp\Client);

        $this->assertSame($http, SMSClientRequest::getHttpClient());
    }

    /** @test */
    public function it_set_verify_ssl_option()
    {
        SMSClientRequest::verify(false);

        $this->assertFalse(SMSClientRequest::getHttpClient()->getConfig('verify'));
    }
}
