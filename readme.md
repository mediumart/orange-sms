# orange-sms

[![Build Status](https://travis-ci.org/mediumart/orange-sms.svg?branch=master)](https://travis-ci.org/mediumart/orange-sms)
[![Code Coverage](https://scrutinizer-ci.com/g/mediumart/orange-sms/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mediumart/orange-sms/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mediumart/orange-sms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediumart/orange-sms/?branch=master)

## Description
A php library to interact with the orange sms api for MiddleEast and Africa.

## Installation

Using composer:
```
$ composer require mediumart/orange-sms
```

## Usage
First you need to resolve a `SMSClient` instance:

```php
use Mediumart\Orange\SMS\SMS;
use Mediumart\Orange\SMS\Http\SMSClient;

/**
* if you already have a valid access token
* */
$client = SMSClient::getInstance('<your_access_token>');

// OR

/**
* directly using <client_id> and <client_secret>
* */
$client = SMSClient::getInstance('<client_id>', '<client_secret>');
```

next step, create an `SMS` object passing it the `$client` :
```php
$sms = new SMS($client);
```
and you're good to go:
```php
// prepare and send an sms in a fluent way
$sms->message('Hello, my dear...')
    ->from('+237690000000')
    ->to('+237670000000')
    ->send();
```

You now have access to the full orange sms api through the `$sms` object  :
```php
// sending SMS.
$response = $sms->to('+237670000000')
                ->from('+237690000000', <'optional_sender_name>')
                ->message('Hello, world!')
                ->send();

// checking your balance(remaining sms units)
// with optional country code filter ie: CIV
$response = $sms->balance('<country_code>');

// checking SMS orders history
// with optional country code filter ie: CMR
$response = $sms->ordersHistory('<country_code>');

// checking SMS statistics
// with optional country code filter
// and optional appID filter
$response = $sms->statistics('<country_code>', '<app_id>');

// setting the SMS DR notification endpoint
// '<your_backend_notification_url>' $url
// '<sender address>' $sender = '+237690000000'
$response = $sms->setDeliveryReceiptNotificationUrl($url, $sender);

// checking the SMS DR notification endpoint
// '<your_last_registered_endpoint_ID>' $id
$response = $sms->checkDeliveryReceiptNotificationUrl($id);

// delete the SMS DR notification endpoint
// '<last_registered_endpoint_ID>' $id
// '<sender address>' $sender = '+237690000000'
$response = $sms->deleteDeliveryReceiptNotificationUrl($id, $sender);

```

All `json` responses will automatically be converted to `array`.

Be sure to lookup [the official documentation](https://developer.orange.com/apis/sms-cm/getting-started), to see what to expect as a `response` to each call.

## Access Token

When you resolve the `SMSClient` instance using your `client_id` and `client_secret`, a new access token will be fetched from the api server and automatically set on the instance, along with its lifetime in seconds.

We recommend saving the token (*maybe to your database*) for future use, at least within  the limit of its validity period. this will help speed up requests to the api.

Use the `getToken()` and `getTokenExpiresIn()` to get those values from the instance:
```php
use Mediumart\Orange\SMS\Http\SMSClient;

$client = SMSClient::getInstance('<client_id>', '<client_secret>');

// get the token
$token = $client->getToken();

// get the token lifetime in seconds
$tokenExpiresIn = $client->getTokenExpiresIn();
```
If you wish, you can also fetch your access token directly without resolving a client instance, using the static `authorize` method:
```php
$response = SMSClient::authorize('<client_id>', '<client_secret>');
```
this will return as `$response`, an array of this form:
```php
[
 "token_type" => "Bearer",
 "access_token" => "i6m2iIcY0SodWSe...L3ojAXXrH",
 "expires_in" => "7776000"
]
```

## SSL Certificate check issue

If you experience ssl certificate checking issue, in your local environment. You can disable the check temporarily with the following line, before starting to interact with the api in your code.

Just for testing purposes though. You should never do this on a production server.
```php
\Mediumart\Orange\SMS\Http\SMSClientRequest::verify(false);
```

## License

Mediumart orange-sms is an open-sourced software licensed under the [MIT license](https://github.com/mediumart/orange-sms/blob/master/LICENSE.txt).
