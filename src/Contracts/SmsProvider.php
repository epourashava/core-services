<?php

namespace Core\Contracts;

use Core\Services\SMS\SMSMessage;

interface SmsProvider
{
    // Base URL for the SMS provider
    // const BASE_URL = '';

    /**
     * SMS Provider constructor.
     *
     * @param array $config ['api_key', 'sender_id']
     */
    public function __construct(array $config);

    /**
     * Send SMS
     * 
     * @param string $to
     * @param SMSMessage $message
     */
    public function send(
        string $to,
        SMSMessage $message
    );

    /**
     * Set the configuration
     * 
     * @param array $config
     * @return $this
     */
    public function setConfig($config): self;
}
