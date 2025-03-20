<?php

namespace Core\Services\Payment;

use Core\Traits\ArrayableEnum;

enum PaymentStatus: string
{
    use ArrayableEnum;

    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';
}
