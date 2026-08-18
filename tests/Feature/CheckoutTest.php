<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_professional_checkout_calculates_price_on_the_server(): void
    {
        config(['services.mercado_pago.access_token' => 'test-token']);
        Http::fake([
            'api.mercadopago.com/checkout/preferences' => Http::response([
                'id' => 'pref-123',
                'sandbox_init_point' => 'https://sandbox.mercadopago.com/checkout/pref-123',
            ]),
        ]);
        $package = Package::where('slug', 'pagina-profesional')->firstOrFail();

        $response = $this->post(route('checkout.store', $package), [
            'name' => 'María López', 'email' => 'maria@example.com',
            'whatsapp' => '+52 999 123 4567', 'country' => 'MX',
            'business_name' => 'Estudio María', 'terms' => '1',
            'price' => '1', 'deposit_amount' => '1',
        ]);

        $response->assertRedirect('https://sandbox.mercadopago.com/checkout/pref-123');
        $order = Order::firstOrFail();
        $this->assertSame('4400.00', $order->total_amount);
        $this->assertSame('2200.00', $order->deposit_amount);
        $this->assertSame(OrderStatus::AwaitingPayment, $order->status);
    }

    public function test_store_package_cannot_enter_direct_checkout(): void
    {
        $package = Package::where('slug', 'tienda-en-linea')->firstOrFail();

        $this->get(route('checkout.show', $package))
            ->assertRedirect(route('home').'#paquetes');
    }

    public function test_honeypot_rejects_automated_checkout(): void
    {
        $package = Package::where('slug', 'landing-page')->firstOrFail();

        $this->post(route('checkout.store', $package), [
            'name' => 'Bot', 'email' => 'bot@example.com', 'whatsapp' => '9991234567',
            'country' => 'MX', 'business_name' => 'Bot Inc', 'terms' => '1',
            'website' => 'https://spam.test',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_unsigned_webhook_is_rejected(): void
    {
        $this->postJson(route('webhooks.mercado-pago'), ['data' => ['id' => '123']])
            ->assertUnauthorized();
    }

    public function test_paypal_return_captures_and_confirms_the_deposit(): void
    {
        config([
            'services.paypal.client_id' => 'client',
            'services.paypal.secret' => 'secret',
            'services.paypal.base_url' => 'https://api-m.sandbox.paypal.com',
        ]);
        $package = Package::where('slug', 'landing-page')->firstOrFail();
        $order = Order::create([
            'reference' => 'XS-TEST-PAYPAL', 'package_id' => $package->id,
            'status' => OrderStatus::AwaitingPayment, 'customer_name' => 'Ana',
            'customer_email' => 'ana@example.com', 'customer_whatsapp' => '5219991234567',
            'country' => 'OTHER', 'business_name' => 'Ana Studio', 'currency' => 'MXN',
            'total_amount' => 2700, 'deposit_amount' => 2700, 'balance_amount' => 0,
        ]);
        $order->payments()->create([
            'provider' => 'paypal', 'provider_reference' => 'PAYPAL-ORDER-1',
            'status' => 'pending', 'currency' => 'MXN', 'amount' => 2700,
        ]);
        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'access']),
            'api-m.sandbox.paypal.com/v2/checkout/orders/PAYPAL-ORDER-1/capture' => Http::response([
                'purchase_units' => [[
                    'custom_id' => 'XS-TEST-PAYPAL',
                    'payments' => ['captures' => [[
                        'id' => 'CAPTURE-1', 'status' => 'COMPLETED',
                        'amount' => ['currency_code' => 'MXN', 'value' => '2700.00'],
                    ]]],
                ]],
            ]),
        ]);

        $this->get(route('checkout.return', ['order' => $order->reference, 'token' => 'PAYPAL-ORDER-1']))
            ->assertOk()->assertSee('Pago confirmado');

        $this->assertSame(OrderStatus::DepositPaid, $order->refresh()->status);
    }
}
