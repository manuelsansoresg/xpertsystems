<?php

namespace App\Services\Payments;

use App\Contracts\PaymentServiceInterface;
use App\Enums\OrderStatus;
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
            'payment_type' => 'full',
            'currency' => $order->currency,
            'amount' => $order->total_amount,
        ]);

        $response = Http::withToken($token)
            ->acceptJson()
            ->withHeaders(['X-Idempotency-Key' => "xs-{$order->reference}-{$payment->id}"])
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'id' => $order->package->slug,
                    'title' => "Plan {$order->package->name} · primer año",
                    'description' => "Pago total del plan para el proyecto {$order->reference}",
                    'quantity' => 1,
                    'currency_id' => $order->currency,
                    'unit_price' => (float) $order->total_amount,
                ]],
                'payer' => ['name' => $order->customer_name, 'email' => $order->customer_email],
                'external_reference' => $order->reference,
                'notification_url' => route('webhooks.mercado-pago'),
                'back_urls' => [
                    'success' => route('checkout.payment.success', ['order' => $order->reference]),
                    'pending' => route('checkout.payment.pending', ['order' => $order->reference]),
                    'failure' => route('checkout.payment.failure', ['order' => $order->reference]),
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

    public function synchronizePayment(Order $order, string $paymentId, ?array $remote = null): Payment
    {
        $token = config('services.mercado_pago.access_token');

        if (! $token) {
            throw new RuntimeException('Mercado Pago aún no está configurado.');
        }

        if (! preg_match('/^\d+$/', $paymentId)) {
            throw new RuntimeException('El identificador del pago no es válido.');
        }

        $remote ??= Http::withToken($token)
            ->acceptJson()
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}")
            ->throw()
            ->json();

        $remoteReference = (string) ($remote['external_reference'] ?? '');
        $remoteAmount = number_format((float) ($remote['transaction_amount'] ?? 0), 2, '.', '');
        $orderAmount = number_format((float) $order->total_amount, 2, '.', '');

        if (
            ! hash_equals($order->reference, $remoteReference)
            || ! hash_equals($orderAmount, $remoteAmount)
            || ($remote['currency_id'] ?? null) !== $order->currency
        ) {
            throw new RuntimeException('El pago recibido no coincide con esta orden.');
        }

        $payment = Payment::query()
            ->where('provider', 'mercado_pago')
            ->where('provider_reference', $paymentId)
            ->first();

        if (! $payment) {
            $payment = $order->payments()
                ->where('provider', 'mercado_pago')
                ->where('status', 'pending')
                ->latest('id')
                ->first() ?? new Payment([
                    'order_id' => $order->id,
                    'provider' => 'mercado_pago',
                    'payment_type' => 'full',
                    'currency' => $order->currency,
                    'amount' => $order->total_amount,
                ]);
        }

        $status = (string) ($remote['status'] ?? 'unknown');
        $payment->fill([
            'order_id' => $order->id,
            'provider_reference' => $paymentId,
            'external_event_id' => "mercado_pago:payment:{$paymentId}",
            'status' => $status,
            'payment_type' => 'full',
            'currency' => $order->currency,
            'amount' => $order->total_amount,
            'paid_at' => $status === 'approved' ? ($payment->paid_at ?? now()) : null,
            'payload' => $remote,
        ])->save();

        if ($status === 'approved') {
            $order->update([
                'status' => OrderStatus::Paid,
                'payment_status' => 'approved',
            ]);
        } else {
            $order->update(['payment_status' => $status]);
        }

        return $payment;
    }
}
