<?php

namespace App\Services\Payments;

use App\Contracts\PaymentServiceInterface;

class PaymentServiceResolver
{
    public function forCountry(string $country): PaymentServiceInterface
    {
        return app(MercadoPagoPaymentService::class);
    }
}
