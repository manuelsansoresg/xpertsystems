<?php

namespace App\Services\Payments;

use App\Contracts\PaymentServiceInterface;

class PaymentServiceResolver
{
    public function forCountry(string $country): PaymentServiceInterface
    {
        return strtoupper($country) === 'MX'
            ? app(MercadoPagoPaymentService::class)
            : app(PayPalPaymentService::class);
    }
}
