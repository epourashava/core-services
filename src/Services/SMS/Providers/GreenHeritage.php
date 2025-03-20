<?php

namespace Core\Services\SMS\Providers;

use Core\Contracts\SmsProvider;
use Core\Services\SMS\SMSMessage;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GreenHeritage implements SmsProvider
{
    /**
     * Base URL for GreenHeritage API - TODO: Update the URL
     * 
     * @var string
     */
    const BASE_URL = 'https://api.greenheritage.net/api/v1/sms/send';

    /**
     * GreenHeritage constructor.
     *
     * @param array $config ['api_key', 'sender_id']
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
        $smsData = $this->getSmsData($message, $to);

        if (empty($message->getContent())) {
            Log::error('SMS content is empty');
            return;
        }

        /**
         * Send the SMS using ElitBuzz API
         * If the API returns an error, it will throw an exception
         * If the API returns a success response, it will return the response
         */
        $result = Http::post(
            self::BASE_URL,
            $smsData
        );

        $result->throw();

        return $result->json();
    }

    /**
     * Get SMS data to send
     * 
     * @param SMSMessage $message
     * @param string $to
     * @return array
     */
    function getSmsData(SMSMessage $message, string $to): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'senderid' => $this->config['sender_id'],
            'contacts' => $to,
            'msg' => $message->getContent(),
            'type' => $this->getType($message->getContent())
        ];
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

    /**
     * Get the type of the SMS
     * 
     * @return string
     */
    function getType($content): string
    {
        return is_unicode($content) ? 'unicode' : 'text';
    }
}
