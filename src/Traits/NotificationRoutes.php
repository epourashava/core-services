<?php

namespace Core\Traits;

/**
 * Trait HasNotification
 * 
 * @package Core\Traits
 */
trait NotificationRoutes
{
    /**
     * Get the phone number to send the SMS notification to.
     * 
     * @param object $notification
     * @return string
     */
    public function routeNotificationForSms($notification): string
    {
        return $this->phone_number ?? "";
    }

    /**
     * Get the email address to send the email notification to.
     * 
     * @param object $notification
     * @return string
     */
    public function routeNotificationForMail($notification): string
    {
        return $this->email ?? "";
    }
}
