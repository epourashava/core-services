<?php

namespace Core\Contracts;

use Core\Services\SMS\SMSMessage;

interface ShouldSendSms
{
    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SMSMessage
     */
    public function toSms(object $notifiable): SMSMessage;

    /**
     * Get the subdomain for the notification.
     *
     * @param  mixed  $notifiable
     * @return string|null
     */
    public function getSubdomain(object $notifiable): ?string;
}
