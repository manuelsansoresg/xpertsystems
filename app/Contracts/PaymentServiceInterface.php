<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentServiceInterface
{
    public function createCheckout(Order $order): Payment;
}
