<?php

namespace Core\Services\Payment;

use Core\Helpers\DataHelper;
use Core\Services\Payment\PaymentStatus;

final class PaymentResponse
{
    public function __construct(
        protected PaymentStatus $status = PaymentStatus::Pending,
        protected array $data = [],
    ) {
        // 
    }

    function setStatus(PaymentStatus $status)
    {
        $this->status = $status;

        return $this;
    }
    function getStatus(): PaymentStatus
    {
        return $this->status;
    }
    function isStatus(PaymentStatus $status): bool
    {
        return $this->status === $status;
    }

    function setTransactionId(string $transactionId)
    {
        $this->data['transaction_id'] = $transactionId;

        return $this;
    }
    function getTransactionId(): string
    {
        return $this->data['transaction_id'] ?? '';
    }

    function setAmount(float $amount)
    {
        $this->data['amount'] = $amount;

        return $this;
    }
    function getAmount(): float
    {
        return $this->data['amount'] ?? 0;
    }

    function setResult($response)
    {
        $this->data['result'] = $response;

        return $this;
    }
    function getResult()
    {
        return new DataHelper($this->data['result'] ?? []);
    }

    function getAdditionalData()
    {
        $data =
            $this->data['result']['additionalMerchantInfo'] ?? [];

        return json_decode($data, 1);
    }

    function setError(string $error)
    {
        $this->data['error'] = $error;

        return $this;
    }
    function getError(): string
    {
        return $this->data['error'] ?? '';
    }

    function toArray()
    {
        return $this->data + [
            'status' => $this->status->value,
        ];
    }
}
