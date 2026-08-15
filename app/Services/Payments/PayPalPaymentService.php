<?php

namespace App\Services\Payments;

use App\Contracts\PaymentServiceInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalPaymentService implements PaymentServiceInterface
{
    public function createCheckout(Order $order): Payment
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (! $clientId || ! $secret) {
            throw new RuntimeException('PayPal aún no está configurado.');
        }

        $baseUrl = config('services.paypal.base_url');
        $accessToken = $this->accessToken();

        $payment = $order->payments()->create([
            'provider' => 'paypal', 'status' => 'pending', 'currency' => $order->currency,
            'amount' => $order->deposit_amount,
        ]);

        $response = Http::withToken($accessToken)->acceptJson()
            ->withHeaders(['PayPal-Request-Id' => "xs-{$order->reference}-{$payment->id}"])
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->reference,
                    'custom_id' => $order->reference,
                    'invoice_id' => $order->reference,
                    'description' => "Anticipo · {$order->package->name}",
                    'amount' => ['currency_code' => $order->currency, 'value' => number_format((float) $order->deposit_amount, 2, '.', '')],
                ]],
                'payment_source' => ['paypal' => ['experience_context' => [
                    'brand_name' => 'XpertSystems', 'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('checkout.return', ['order' => $order->reference]),
                    'cancel_url' => route('checkout.show', $order->package),
                ]]],
            ])->throw()->json();

        $approveUrl = data_get(collect($response['links'] ?? [])->firstWhere('rel', 'payer-action'), 'href')
            ?? data_get(collect($response['links'] ?? [])->firstWhere('rel', 'approve'), 'href');

        return tap($payment)->update([
            'provider_reference' => $response['id'] ?? null,
            'checkout_url' => $approveUrl,
            'payload' => $response,
        ]);
    }

    public function capture(Order $order, string $providerOrderId): Payment
    {
        $payment = $order->payments()->where('provider', 'paypal')
            ->where('provider_reference', $providerOrderId)->firstOrFail();
        $baseUrl = config('services.paypal.base_url');

        $response = Http::withToken($this->accessToken())->acceptJson()
            ->withHeaders(['PayPal-Request-Id' => "xs-capture-{$order->reference}"])
            ->send('POST', "{$baseUrl}/v2/checkout/orders/{$providerOrderId}/capture", ['body' => '{}'])
            ->throw()->json();

        $purchaseUnit = $response['purchase_units'][0] ?? [];
        $capture = $purchaseUnit['payments']['captures'][0] ?? [];
        $amount = $capture['amount']['value'] ?? 0;
        $currency = $capture['amount']['currency_code'] ?? '';
        $reference = $purchaseUnit['custom_id'] ?? $purchaseUnit['invoice_id'] ?? null;

        if (
            ! hash_equals($order->reference, (string) $reference)
            || ! hash_equals(number_format((float) $order->deposit_amount, 2, '.', ''), number_format((float) $amount, 2, '.', ''))
            || $currency !== $order->currency
            || ($capture['status'] ?? null) !== 'COMPLETED'
        ) {
            throw new RuntimeException('PayPal devolvió una captura que no coincide con la orden.');
        }

        $payment->update(['status' => 'completed', 'paid_at' => now(), 'payload' => $response]);
        $order->update(['status' => \App\Enums\OrderStatus::DepositPaid]);

        return $payment->refresh();
    }

    private function accessToken(): string
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (! $clientId || ! $secret) {
            throw new RuntimeException('PayPal aún no está configurado.');
        }

        return Http::withBasicAuth($clientId, $secret)
            ->asForm()->post(config('services.paypal.base_url').'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->throw()->json('access_token');
    }
}
