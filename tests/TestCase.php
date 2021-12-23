<?php

namespace Orange\SMS\Tests;

use \Mockery as m;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;

class TestCase extends \PHPUnit\Framework\TestCase
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
     * @var string
     */
    protected $sender = '+237690000000';

    /**
     * @var string
     */
    protected $callbackUri = 'https://test';

    /**
     * @var string
     */
    protected $callbackUriNotSecured = 'http://test';


    /**
     * @var string
     */
    protected $smsDrSubscriptionID = '56e19nt197703244e46181c8';

    /**
     * cleanUp
     */
    public function tearDown(): void
    {
        m::close();

        \Mediumart\Orange\SMS\Http\SMSClientRequest::verify(true);

        parent::tearDown();
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
     * @return m\MockInterface
     */
    public function mockGuzzleHttpClientRequest()
    {
        $client = m::mock(new \GuzzleHttp\Client);

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
     * @throws \Exception
     */
    public function mockGuzzleHttpClientResponse($method, $uri, $options)
    {
        if (!$this->context) {
            throw new \Exception('Request context need to be set before mocking response.');
        }

        switch ($this->context) {
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
            case 'setDeliveryReceiptNotificationUrl':
                return $this->handleSMSDRRegisterCallbackRequestResponse($method, $uri, $options);
                break;
            case 'checkDeliveryReceiptNotificationUrl':
                return $this->handleSMSDRCheckCallbackRequestResponse($method, $uri, $options);
                break;
            case 'deleteDeliveryReceiptNotificationUrl':
                return $this->handleSMSDRDeleteCallbackRequestResponse($method, $uri, $options);
                break;
        }
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleAuthorizationRequestResponse($method, $uri, $options)
    {
        if ($method !== 'POST') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== 'https://api.orange.com/oauth/v3/token') {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockAuthorizationRequestOptions()) {
            throw new ClientException('No matching options', new Request('*', '*'), new Response);
        }

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

        if ($method !== 'GET') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== 'https://api.orange.com/sms/admin/v1/contracts') {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockGenericRequestOptions()) {
            throw new ClientException('No matching options', new Request('*', '*'), new Response);
        }

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

        if ($method !== 'GET') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== 'https://api.orange.com/sms/admin/v1/purchaseorders') {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockGenericRequestOptions()) {
            throw new ClientException('No matching options', new Request('*', '*'), new Response);
        }

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

        if ($method !== 'GET') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== 'https://api.orange.com/sms/admin/v1/statistics') {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockGenericRequestOptions()) {
            throw new ClientException('No matching options', new Request('*', '*'), new Response);
        }

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
        if (!$this->checkOutboundSmsRequestOptions($options)) {
            throw new ClientException('No matching options', new Request('*', '*'), new Response);
        }

        $body = json_decode($options['body'], true);

        $sender = $body['outboundSMSMessageRequest']['senderAddress'];
        $endpoint = "https://api.orange.com/smsmessaging/v1/outbound/".urlencode($sender)."/requests";

        if ($method !== 'POST') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== $endpoint) {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        return $this->successResponse(['outboundSMSMessageRequest' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleSMSDRRegisterCallbackRequestResponse($method, $uri, $options)
    {
        if ($method !== 'POST') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if (
            $uri !== 'https://api.orange.com/smsmessaging/v1/outbound/'
            .urlencode('tel:'.$this->sender).'/subscriptions'
        ) {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockSMSDRRegisterCallbackRequestOptions()) {
            throw new ClientException('no matching options', new Request('*', '*'), new Response);
        }

        return $this->successResponse(['deliveryReceiptSubscription' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleSMSDRCheckCallbackRequestResponse($method, $uri, $options)
    {
        if ($method !== 'GET') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if ($uri !== 'https://api.orange.com/smsmessaging/v1/outbound/subscriptions/'.$this->smsDrSubscriptionID) {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockSMSDRCheckAndDeleteOptions()) {
            throw new ClientException('no matching options', new Request('*', '*'), new Response);
        }

        return $this->successResponse(['deliveryReceiptSubscription' => []]);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function handleSMSDRDeleteCallbackRequestResponse($method, $uri, $options)
    {
        if ($method !== 'DELETE') {
            throw new ClientException('wrong method', new Request('*', '*'), new Response);
        }

        if (
            $uri !== 'https://api.orange.com/smsmessaging/v1/outbound/'
            .urlencode('tel:'.$this->sender).'/subscriptions/'.$this->smsDrSubscriptionID
        ) {
            throw new ClientException('wrong uri', new Request('*', '*'), new Response);
        }

        if ($options != $this->mockSMSDRCheckAndDeleteOptions()) {
            throw new ClientException('no matching options', new Request('*', '*'), new Response);
        }

        return $this->successResponse([], 204, [], 'No Content');
    }

    /**
     * @param array $body
     * @param int $statusCode
     * @param null $headers
     * @param null $reason
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function successResponse(array $body, $statusCode = 200, $headers = null, $reason = null)
    {
        $body = !empty($body) ? json_encode($body) : null;

        $headers = $headers ?: ['Content-Type' => 'application/json'];

        return new Response($statusCode, $headers, $body, '1.1', $reason);
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
    private function mockSMSDRRegisterCallbackRequestOptions()
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer i6m2iIcY0SodWSe...L3ojAXXrH'
            ],
            'body' => json_encode([
                "deliveryReceiptSubscription" => [
                    "callbackReference" => [
                        "notifyURL" => $this->callbackUri
                    ]
                ]
            ])
        ];
    }

    /**
     * @return array
     */
    private function mockSMSDRCheckAndDeleteOptions()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer i6m2iIcY0SodWSe...L3ojAXXrH',
                'Content-Type' => 'application/json'
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
        if (!isset($options['body'])) {
            return false;
        }

        $body = json_decode($options['body'], true);

        return !empty($body['outboundSMSMessageRequest']['address']) &&
               !empty($body['outboundSMSMessageRequest']['senderAddress']) &&
               !empty($body['outboundSMSMessageRequest']['outboundSMSTextMessage']['message']);
    }
}
