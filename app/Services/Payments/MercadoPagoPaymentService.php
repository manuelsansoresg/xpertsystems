<?php

namespace App\Services\Payments;

use App\Contracts\PaymentServiceInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MercadoPagoPaymentService implements PaymentServiceInterface
{
    public function createCheckout(Order $order): Payment
    {
        $token = config('services.mercado_pago.access_token');

        if (! $token) {
            throw new RuntimeException('Mercado Pago aún no está configurado.');
        }

        $payment = $order->payments()->create([
            'provider' => 'mercado_pago',
            'status' => 'pending',
            'currency' => $order->currency,
            'amount' => $order->deposit_amount,
        ]);

        $response = Http::withToken($token)
            ->acceptJson()
            ->withHeaders(['X-Idempotency-Key' => "xs-{$order->reference}-{$payment->id}"])
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'id' => $order->package->slug,
                    'title' => "Anticipo · {$order->package->name}",
                    'description' => "Anticipo del 50% para el proyecto {$order->reference}",
                    'quantity' => 1,
                    'currency_id' => $order->currency,
                    'unit_price' => (float) $order->deposit_amount,
                ]],
                'payer' => ['name' => $order->customer_name, 'email' => $order->customer_email],
                'external_reference' => $order->reference,
                'notification_url' => route('webhooks.mercado-pago'),
                'back_urls' => [
                    'success' => route('checkout.return', ['order' => $order->reference]),
                    'pending' => route('checkout.return', ['order' => $order->reference]),
                    'failure' => route('checkout.show', $order->package),
                ],
                'auto_return' => 'approved',
                'statement_descriptor' => 'XPERTSYSTEMS',
            ])->throw()->json();

        return tap($payment)->update([
            'provider_reference' => $response['id'] ?? null,
            'checkout_url' => app()->isProduction() ? ($response['init_point'] ?? null) : ($response['sandbox_init_point'] ?? $response['init_point'] ?? null),
            'payload' => $response,
        ]);
    }
}
