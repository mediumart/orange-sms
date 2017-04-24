<?php

namespace Mediumart\Orange\SMS;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Mediumart\Orange\SMS\Http\SMSClient;
use Mediumart\Orange\SMS\Exceptions\InvalidCredentialsException;

class OrangeSmsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register Services boundaries
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('orange-sms-client', function($app) {
           return SMSClient::getInstance($this->getClientToken());
        });

        $this->app->bind('orange-sms', function ($app) {
            return new SMS($app->make('orange-sms-client'));
        });

        $this->app->alias('orange-sms', SMS::class);

        $this->app->alias('orange-sms-client', SMSClient::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'orange-sms',
            'orange-sms-client',
            SMSClient::class,
            SMS::Class,
        ];
    }

    /**
     * Get the token from the cache.
     *
     * @return mixed
     */
    public function getClientToken()
    {
        // 90 days.
        $api_token_std_expires_time_in_seconds = 7776000;

        // cache duration: 89 days.
        $minutes = Carbon::now()->addMinutes(
            ($api_token_std_expires_time_in_seconds/60) - (24*60)
        )->diffInMinutes();

        // cache the token and return it.
        return Cache::remember('orange.sms.token', $minutes, function () {
            $response = $this->authorize();

            return isset($response['access_token'])
                ? $response['access_token']
                : $this->throwsException($response);
        });
    }

    /**
     * @return array
     */
    public function authorize()
    {
        return SMSClient::authorize(
            config('services.orange.sms.client_id'),
            config('services.orange.sms.client_secret')
        );
    }

    /**
     * Throws exceptions.
     *
     * @param $response
     * @throws \Mediumart\Orange\SMS\Exceptions\InvalidCredentialsException
     */
    protected function throwsException($response)
    {
        if (isset($response['error']) && $response['error'] === 'invalid_client') {
            throw new InvalidCredentialsException($response['error_description']);
        }
    }
}
