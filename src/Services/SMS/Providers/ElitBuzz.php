<?php

namespace Core\Services\SMS\Providers;

use Core\Contracts\SmsProvider;
use Core\Services\SMS\SMSMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElitBuzz implements SmsProvider
{
    /**
     * Base URL for ElitBuzz API
     * 
     * @var string
     */
    const BASE_URL = 'https://msg.elitbuzz-bd.com';

    /**
     * ElitBuzz constructor.
     *
     * @param array $config ['api_key', 'sender_id']
     */
    function __construct(protected array $config) {}

    /**
     * Set the configuration
     * 
     * @param array $config
     * @return $this
     */
    function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Send SMS
     * 
     * @param string $to
     * @param SmsMessage $message
     * @throws InvalidArgumentException
     */
    public function send(string $to, SMSMessage $message)
    {
        $smsData = $this->getSmsData($message, $to);

        if ($this->validate($smsData)) {
            Log::error('SMS content is empty');
            return;
        }

        $result = Http::post(
            $this->getUri('/smsapi'), // for single SMS
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
            'type' => 'unicode' // $this->getType($message->getContent())
        ];
    }

    /**
     * Validate the SMS data
     * If the message, api_key, senderid, to is empty, it will return true
     * 
     * @param array $data
     * @return bool
     */
    function validate(array $data): bool
    {
        return empty($data['msg']) || empty($data['api_key']) || empty($data['senderid']) || empty($data['contacts']);
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

    /**
     * Get the full URL for the API
     * 
     * @param string $path
     * @return string
     */
    function getUri(string $path): string
    {
        return self::BASE_URL . $path;
    }
}
