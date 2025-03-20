<?php

namespace Core\Services\Payment\Gateway;

use Core\Contracts\PaymentGateway;
use Core\Enums\PaymentStatus;
use Core\Services\Payment\PaymentResponse;
use Core\Services\Tenant;
use Illuminate\Support\Facades\Log;
use Softscholar\Payment\Services\Gateways\Nagad\Nagad as NagadProvider;

class Nagad implements PaymentGateway
{
    protected $nagadInstance;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->nagadInstance = app(NagadProvider::class);
    }

    /**
     * Initiate the payment
     *
     * @param array $data - ['order_id', 'customer_id', 'amount', 'additional_info']
     * @return string
     */
    public function initiate(array $data = [])
    {
        try {
            $data = array_merge([
                'callback_url' => route(
                    'payment.callback',
                    Tenant::routeParams()
                ),
            ], $data);

            // initiate the payment and get the redirect URL
            $url = $this->nagadInstance->checkout($data);
            return [
                'redirect_url' => $url
            ];
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return [
                'redirect_url' => null,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Verify the payment
     *
     * @param string $tnxId
     * @return PaymentResponse
     */
    public function verify($tnxId): PaymentResponse
    {
        try {
            $response = $this->nagadInstance->verify($tnxId);

            return (new PaymentResponse(PaymentStatus::Success))
                ->setTransactionId($response['paymentRefId'] ?? '')
                ->setAmount($response['amount'] ?? 0)
                ->setResult($response);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            return (new PaymentResponse(PaymentStatus::Failed))
                ->setError($th->getMessage());
        }
    }

    /**
     * Get the request key
     *
     * @return string
     */
    public function getRequestKey(): string
    {
        return 'payment_ref_id';
    }
}
