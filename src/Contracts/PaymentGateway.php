<?php

namespace Core\Contracts;

use Core\Services\Payment\PaymentResponse;

interface PaymentGateway
{
    /**
     * Initiate the payment
     *
     * @return mixed
     */
    public function initiate(array $data = []);

    /**
     * Verify the payment
     *
     * @return mixed
     */
    public function verify(string $tnxId): PaymentResponse;

    /**
     * Get the request key
     *
     * @return string
     */
    public function getRequestKey(): string;
}
