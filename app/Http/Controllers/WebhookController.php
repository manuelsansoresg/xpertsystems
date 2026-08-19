<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\MercadoPagoPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class WebhookController extends Controller
{
    public function mercadoPago(Request $request, MercadoPagoPaymentService $mercadoPago): JsonResponse
    {
        $dataId = (string) (
            $request->query('data.id')
            ?? $request->input('data.id')
            ?? $request->query('id')
            ?? $request->input('id')
        );
        abort_unless($this->validMercadoPagoSignature($request, $dataId), 401);

        $token = config('services.mercado_pago.access_token');
        $remote = Http::withToken($token)->acceptJson()
            ->get("https://api.mercadopago.com/v1/payments/{$dataId}")->throw()->json();

        $order = Order::query()->where('reference', $remote['external_reference'] ?? '')->firstOrFail();
        try {
            $mercadoPago->synchronizePayment($order, $dataId, $remote);
        } catch (Throwable $exception) {
            report($exception);
            abort(422);
        }

        return response()->json(['received' => true]);
    }

    public function paypal(Request $request): JsonResponse
    {
        abort_unless($this->validPayPalSignature($request), 401);

        if ($request->input('event_type') !== 'PAYMENT.CAPTURE.COMPLETED') {
            return response()->json(['received' => true]);
        }

        $resource = $request->input('resource', []);
        $reference = $resource['custom_id'] ?? $resource['invoice_id'] ?? null;
        $order = Order::query()->where('reference', $reference)->firstOrFail();
        $amount = $resource['amount']['value'] ?? 0;
        $currency = $resource['amount']['currency_code'] ?? '';

        abort_unless(
            hash_equals(number_format((float) $order->total_amount, 2, '.', ''), number_format((float) $amount, 2, '.', ''))
            && $currency === $order->currency,
            422
        );

        $payment = Payment::query()->where('order_id', $order->id)->where('provider', 'paypal')->latest()->first()
            ?? new Payment(['order_id' => $order->id, 'provider' => 'paypal']);
        $payment->fill(['status' => 'completed', 'payment_type' => 'full', 'currency' => $currency, 'amount' => $amount, 'paid_at' => now(), 'payload' => $request->all()])->save();
        $order->update(['status' => OrderStatus::Paid]);

        return response()->json(['received' => true]);
    }

    private function validMercadoPagoSignature(Request $request, string $dataId): bool
    {
        $secret = config('services.mercado_pago.webhook_secret');
        $signature = (string) $request->header('x-signature');
        $requestId = (string) $request->header('x-request-id');

        if (! $dataId) {
            return false;
        }

        // La consulta autenticada del pago, la referencia, la moneda y el importe
        // se validan siempre. Si se configuró la firma de Webhooks, también se exige.
        if (! $secret) {
            return true;
        }

        if (! $signature || ! $requestId) {
            return false;
        }

        parse_str(str_replace(',', '&', $signature), $parts);
        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;
        $manifest = 'id:'.strtolower($dataId).";request-id:{$requestId};ts:{$ts};";

        return is_string($v1) && hash_equals(hash_hmac('sha256', $manifest, $secret), $v1);
    }

    private function validPayPalSignature(Request $request): bool
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');
        $webhookId = config('services.paypal.webhook_id');

        if (! $clientId || ! $secret || ! $webhookId) {
            return false;
        }

        $baseUrl = config('services.paypal.base_url');
        $token = Http::withBasicAuth($clientId, $secret)->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials'])
            ->throw()->json('access_token');

        $verification = Http::withToken($token)->acceptJson()
            ->post("{$baseUrl}/v1/notifications/verify-webhook-signature", [
                'auth_algo' => $request->header('paypal-auth-algo'),
                'cert_url' => $request->header('paypal-cert-url'),
                'transmission_id' => $request->header('paypal-transmission-id'),
                'transmission_sig' => $request->header('paypal-transmission-sig'),
                'transmission_time' => $request->header('paypal-transmission-time'),
                'webhook_id' => $webhookId,
                'webhook_event' => $request->all(),
            ])->throw()->json();

        return ($verification['verification_status'] ?? null) === 'SUCCESS';
    }
}
