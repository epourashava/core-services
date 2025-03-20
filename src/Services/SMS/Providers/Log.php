<?php

namespace Core\Services\SMS\Providers;

use Core\Contracts\SmsProvider;
use Core\Services\SMS\SMSMessage;
use Exception;
use Illuminate\Support\Facades\Log as Logger;

class Log implements SmsProvider
{
    /**
     * Log SMS provider constructor.
     *
     */
    function __construct(protected array $config) {}

    /**
     * Send SMS
     * 
     * @param string $to
     * @param SmsMessage $message
     * @throws Exception
     */
    public function send(string $to, SMSMessage $message)
    {
        Logger::info("[Driver:Log] - SMS sent to: {$to} with message: {$message->getContent()}");
    }

    /**
     * Set the configuration
     * 
     * @param array $config
     */
    function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }
}
