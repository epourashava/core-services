<?php

namespace Core\Services\SMS;

use Core\Services\Tenant;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * Send the given notification.
     * 
     * @param  \Core\Models\Customer|\Core\Models\User  $notifiable
     * @param  \Core\Contracts\ShouldSendSms|Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toSms')) {
            /**
             * Get the settings of the tenant
             * 
             * @var \Core\Helpers\DataHelper $settings
             */
            $settings = Tenant::setSubDomain(
                $notification->getSubdomain($notifiable)
            )->getSettings();

            $phoneNumber = $notifiable->routeNotificationFor('sms', $notification);

            if (!$phoneNumber) {
                return;
            }

            $message = $notification->toSms($notifiable);

            app(SmsManager::class)
                ->driver($settings->get('sms_gateway', $message->getDriver()))
                ->setConfig([
                    'api_key' => $settings->get('sms_api_key'),
                    'sender_id' =>
                    $settings->get('sms_sending_type') === SmsEnum::MASKED ?
                        $settings->get('sms_sender_name') :
                        $settings->get('sms_sender_number')
                ])
                ->send(
                    $phoneNumber,
                    $message
                );
        }
    }
}
