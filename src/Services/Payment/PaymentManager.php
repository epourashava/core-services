<?php

namespace Core\Services\Payment;

use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'nagad';
    }

    /**
     * Create an instance of the Nagad driver
     *
     * @return Gateway\Nagad
     */
    public function createNagadDriver()
    {
        return new Gateway\Nagad();
    }
}
