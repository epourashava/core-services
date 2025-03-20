<?php

namespace Core\Services\SMS;

use Illuminate\Support\Manager;

class SmsManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $systemDefault = $this->config->get(
            'services.sms.default',
            SmsEnum::ELITBUZZ
        );

        return settings('sms_gateway', $systemDefault);
    }

    /**
     * Create an instance of the ElitBuzz driver.
     *
     * @return \Core\Services\SMS\ElitBuzzSMSService
     */
    protected function createElitbuzzDriver()
    {
        return new Providers\ElitBuzz(
            [
                'api_key' => settings('sms_api_key'),
                'sender_id' => settings('sms_sending_type') === SmsEnum::MASKED->value ?
                    settings('sms_sender_name') :
                    settings('sms_sender_number')
            ]
        );
    }

    /**
     * Create an instance of the GreenHeritage driver.
     *
     * @return \Core\Services\SMS\GreenHeritageSMSService
     */
    protected function createGreenheritageDriver()
    {
        return new Providers\GreenHeritage(
            [
                'api_key' => settings('sms_api_key'),
                'sender_id' => settings('sms_sending_type') === SmsEnum::MASKED->value ?
                    settings('sms_sender_name') :
                    settings('sms_sender_number')
            ]
        );
    }

    /**
     * Create an instance of the Log driver.
     *
     * @return \Core\Services\SMS\Providers\Log
     */
    protected function createLogDriver()
    {
        return new Providers\Log([]);
    }
}
