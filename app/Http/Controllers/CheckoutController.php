<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderCheckoutStarted;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use App\Services\Payments\MercadoPagoPaymentService;
use App\Services\Payments\PaymentServiceResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request, Package $package, PaymentServiceResolver $resolver): RedirectResponse
    {
        abort_unless($package->active && $package->direct_checkout, 404);
        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $package, $request) {
            $lead = Lead::create([
                'name' => $data['name'], 'email' => $data['email'], 'whatsapp' => $data['whatsapp'],
                'country' => $data['country'], 'business_name' => $data['business_name'] ?? null,
                'package_id' => $package->id, 'source' => 'checkout',
                'metadata' => ['referrer' => $request->headers->get('referer')],
            ]);

            $total = (float) $package->price;

            return Order::create([
                'reference' => 'XS-'.now()->format('ymd').'-'.Str::upper(Str::random(8)),
                'package_id' => $package->id, 'lead_id' => $lead->id,
                'status' => OrderStatus::AwaitingPayment,
                'customer_name' => $data['name'], 'customer_email' => $data['email'],
                'customer_whatsapp' => $data['whatsapp'], 'country' => $data['country'],
                'business_name' => $data['business_name'] ?? null, 'currency' => $package->currency,
                'total_amount' => $total, 'deposit_amount' => $total,
                'balance_amount' => 0,
            ]);
        });

        try {
            $payment = $resolver->forCountry($data['country'])->createCheckout($order->load('package'));

            if (! $payment->checkout_url) {
                throw new \RuntimeException('No se recibió una liga de pago.');
            }

        } catch (Throwable $exception) {
            report($exception);

            $returnRoute = ($data['checkout_source'] ?? 'home') === 'precios' ? 'precios' : 'home';

            return redirect(route($returnRoute, ['checkout' => $package->slug]).'#paquetes')
                ->withInput($request->except(['terms', 'website']))
                ->with('payment_error', 'Guardamos tus datos, pero el pago todavía no está disponible. Escríbenos por WhatsApp y te ayudamos a continuar.');
        }

        try {
            Mail::to($order->customer_email)->send(new OrderCheckoutStarted($order, $payment->checkout_url));
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()->away($payment->checkout_url);
    }

    public function paymentResult(
        Request $request,
        string $order,
        string $result,
        MercadoPagoPaymentService $mercadoPago,
    ): View {
        $order = Order::query()->where('reference', $order)->with(['package', 'payments'])->firstOrFail();

        if ($request->filled('payment_id') && ! in_array($order->status, [OrderStatus::Paid, OrderStatus::DepositPaid], true)) {
            try {
                $mercadoPago->synchronizePayment($order, (string) $request->query('payment_id'));
                $order->refresh()->load(['package', 'payments']);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $state = match (true) {
            in_array($order->status, [OrderStatus::Paid, OrderStatus::DepositPaid], true) => 'success',
            in_array($order->payment_status, ['rejected', 'cancelled', 'refunded', 'charged_back'], true) => 'failure',
            $result === 'failure' => 'failure',
            default => 'pending',
        };

        return view('checkout.return', [
            'order' => $order,
            'state' => $state,
            'whatsapp' => $this->whatsapp(),
        ]);
    }

    private function whatsapp(): string
    {
        $stored = Setting::query()->where('key', 'whatsapp_number')->value('value');

        return preg_replace('/\D+/', '', (string) ($stored ?: config('xpertsystems.whatsapp_number')));
    }
}
