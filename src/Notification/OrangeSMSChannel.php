<?php

namespace Mediumart\Orange\SMS\Notification;

use Illuminate\Support\Facades\App;
use Illuminate\Notifications\Notification;
use Mediumart\Notifier\Contracts\Channels\Channel;
use Mediumart\Orange\SMS\SMS;

class OrangeSMSChannel implements Channel
{
    /**
     * @var \Mediumart\Orange\SMS\SMS
     */
    private $client;

    /**
     * OrangeSMSChannel constructor.
     * @param \Mediumart\Orange\SMS\SMS $client
     */
    public function __construct(SMS $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toOrange($notifiable);

        if($message) {
            $this->client->to($message->to)
                        ->from($message->from)
                        ->message($message->text)
                        ->send();
        }
    }

    /**
     * Check for the driver capacity.
     *
     * @param  string $driver
     * @return bool
     */
    public static function canHandleNotification($driver)
    {
        return in_array($driver, ['orange']);
    }

    /**
     * Create a new driver instance.
     *
     * @param  $driver
     * @return \Mediumart\Notifier\Contracts\Channels\Dispatcher
     */
    public static function createDriver($driver)
    {
        return static::canHandleNotification($driver)
            ? new static(App::make('orange-sms')) : null;
    }
}