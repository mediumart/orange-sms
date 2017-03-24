<?php

namespace Tests;

use \Mockery as m;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $token = 'i6m2iIcY0SodWSe...L3ojAXXrH';

    /**
     * @var string
     */
    protected $clientID = 'client_id';

    /**
     * @var string
     */
    protected $clientSecret = 'client_secret';

    /**
     * cleanUp
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * setUp
     *
     * @param $context
     */
    protected function setupRequestContext($context)
    {
        $this->context = $context;

        $client = $this->mockGuzzleHttpClientRequest();

        \Mediumart\Orange\SMS\Http\SMSClientRequest::setHttpClient($client);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function mockGuzzleHttpClientResponse($method, $uri, $options)
    {
        if(!$this->context) throw new \Exception('Request context need to be set before mocking response.');

        switch ($this->context)
        {
            case 'authorization':
                return $this->handleAuthorizationRequestResponse($method, $uri, $options);
                break;
            case 'contracts':
                return $this->handleContractsRequestResponse($method, $uri, $options);
                break;
            case 'ordersHistory':
                return $this->handleOrdersHistoryRequestResponse($method, $uri, $options);
                break;
            case 'outboundSms':
                return $this->handleOutboundSmsRequestResponse($method, $uri, $options);
                break;
            case 'statistics':
                return $this->handleStatisticsRequestResponse($method, $uri, $options);
                break;
        }
    }

    /**
     * @return m\MockInterface
     */
    public function mockGuzzleHttpClientRequest()
    {
        $client = m::mock('\GuzzleHttp\Client');

        $client->shouldReceive('request')->with(
            \Mockery::type('string'),
            \Mockery::type('string'),
            \Mockery::type('array')
        )->andReturnUsing([$this, 'mockGuzzleHttpClientResponse']);

        return $client;
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleAuthorizationRequestResponse($method, $uri, $options)
    {
        if (
            $method !== 'POST' ||
            $uri !== 'https://api.orange.com/oauth/v2/token' ||
            $options !== $this->mockAuthorizationRequestOptions()
        )
            throw new ClientException('*', new Request('*', '*'));

        return $this->successResponse([
            "token_type" => "Bearer",
            "access_token" => "i6m2iIcY0SodWSe...L3ojAXXrH",
            "expires_in" => "7776000"
        ]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleContractsRequestResponse($method, $uri, $options)
    {
        $uri = explode('?', $uri)[0];

        if(
            $method !== 'GET' ||
            $uri !== 'https://api.orange.com/sms/admin/v1/contracts' ||
            $options !== $this->mockGenericRequestOptions()
        )
            throw new ClientException('*', new Request('*', '*'));

        return $this->successResponse(['partnerContracts' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleOrdersHistoryRequestResponse($method, $uri, $options)
    {
        $uri = explode('?', $uri)[0];

        if(
            $method !== 'GET' ||
            $uri !== 'https://api.orange.com/sms/admin/v1/purchaseorders' ||
            $options !== $this->mockGenericRequestOptions()
        )
            throw new ClientException('*', new Request('*', '*'));

        return $this->successResponse(['purchaseOrders' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleStatisticsRequestResponse($method, $uri, $options)
    {
        $uri = explode('?', $uri)[0];

        if(
            $method !== 'GET' ||
            $uri !== 'https://api.orange.com/sms/admin/v1/statistics' ||
            $options !== $this->mockGenericRequestOptions()
        )
            throw new ClientException('*', new Request('*', '*'));

        return $this->successResponse(['partnerStatistics' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleOutboundSmsRequestResponse($method, $uri, $options)
    {
        if(!$this->checkOutboundSmsRequestOptions($options))
            throw new ClientException('*', new Request('*', '*'));

        $body = json_decode($options['body'], true);

        $sender = $body['outboundSMSMessageRequest']['senderAddress'];
        $endpoint = "https://api.orange.com/smsmessaging/v1/outbound/".urlencode($sender)."/requests";

        if($method !== 'POST' || $uri !== $endpoint)
            throw new ClientException('*', new Request('*', '*'));

        return $this->successResponse(['outboundSMSMessageRequest' => []]);
    }

    /**
     * @param array $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function successResponse(array $body)
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($body));
    }

    /**
     * @return array
     */
    private function mockAuthorizationRequestOptions()
    {
        return [
            'headers' => [
                'Authorization' => "Basic " . base64_encode("{$this->clientID}:{$this->clientSecret}")
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ];
    }

    /**
     * @return array
     */
    private function mockGenericRequestOptions()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer i6m2iIcY0SodWSe...L3ojAXXrH'
            ]
        ];
    }

    /**
     * @param $options
     * @return bool
     */
    private function checkOutboundSmsRequestOptions($options)
    {
        if(!isset($options['body'])) return false;

        $body = json_decode($options['body'], true);

        return !empty($body['outboundSMSMessageRequest']['address']) &&
               !empty($body['outboundSMSMessageRequest']['senderAddress']) &&
               !empty($body['outboundSMSMessageRequest']['outboundSMSTextMessage']['message']);
    }
}