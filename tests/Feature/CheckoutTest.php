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
        $this->assertSame('4400.00', $order->deposit_amount);
        $this->assertSame('0.00', $order->balance_amount);
        $this->assertSame(OrderStatus::AwaitingPayment, $order->status);

        Http::assertSent(function ($request) use ($order): bool {
            return $request->url() === 'https://api.mercadopago.com/checkout/preferences'
                && $request['external_reference'] === $order->reference
                && $request['items'][0]['unit_price'] === 4400.0
                && $request['items'][0]['title'] === 'Plan Página Profesional · primer año'
                && $request['back_urls']['success'] === route('checkout.payment.success', $order->reference)
                && $request['back_urls']['pending'] === route('checkout.payment.pending', $order->reference)
                && $request['back_urls']['failure'] === route('checkout.payment.failure', $order->reference);
        });
    }

    public function test_checkout_can_proceed_without_a_business_name(): void
    {
        config(['services.mercado_pago.access_token' => 'test-token']);
        Http::fake([
            'api.mercadopago.com/checkout/preferences' => Http::response([
                'id' => 'pref-without-business',
                'sandbox_init_point' => 'https://sandbox.mercadopago.com/checkout/pref-without-business',
            ]),
        ]);
        $package = Package::where('slug', 'landing-page')->firstOrFail();

        $response = $this->post(route('checkout.store', $package), [
            'name' => 'María López',
            'email' => 'maria@example.com',
            'whatsapp' => '+52 999 123 4567',
            'country' => 'MX',
            'terms' => '1',
        ]);

        $response->assertRedirect('https://sandbox.mercadopago.com/checkout/pref-without-business');
        $this->assertDatabaseHas('leads', [
            'email' => 'maria@example.com',
            'business_name' => null,
        ]);
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'maria@example.com',
            'business_name' => null,
        ]);
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
        config(['services.mercado_pago.webhook_secret' => 'webhook-secret']);

        $this->postJson(route('webhooks.mercado-pago'), ['data' => ['id' => '123']])
            ->assertUnauthorized();
    }

    public function test_webhook_without_optional_secret_still_verifies_payment_with_api(): void
    {
        config([
            'services.mercado_pago.access_token' => 'test-token',
            'services.mercado_pago.webhook_secret' => null,
        ]);
        $package = Package::where('slug', 'landing-page')->firstOrFail();
        $order = Order::create([
            'reference' => 'XS-TEST-WEBHOOK', 'package_id' => $package->id,
            'status' => OrderStatus::AwaitingPayment, 'customer_name' => 'Ana',
            'customer_email' => 'ana@example.com', 'customer_whatsapp' => '5219991234567',
            'country' => 'MX', 'business_name' => 'Ana Studio', 'currency' => 'MXN',
            'total_amount' => 2700, 'deposit_amount' => 2700, 'balance_amount' => 0,
        ]);
        $order->payments()->create([
            'provider' => 'mercado_pago', 'provider_reference' => 'PREFERENCE-WEBHOOK',
            'status' => 'pending', 'currency' => 'MXN', 'amount' => 2700,
        ]);
        Http::fake([
            'api.mercadopago.com/v1/payments/123456' => Http::response([
                'id' => 123456, 'status' => 'approved',
                'external_reference' => 'XS-TEST-WEBHOOK',
                'transaction_amount' => 2700, 'currency_id' => 'MXN',
            ]),
        ]);

        $this->postJson(route('webhooks.mercado-pago'), ['data' => ['id' => '123456']])
            ->assertOk()
            ->assertJson(['received' => true]);

        $this->assertSame(OrderStatus::Paid, $order->refresh()->status);
        Http::assertSentCount(1);
    }

    public function test_mercado_pago_return_verifies_and_confirms_the_full_plan_payment(): void
    {
        config(['services.mercado_pago.access_token' => 'test-token']);
        $package = Package::where('slug', 'landing-page')->firstOrFail();
        $order = Order::create([
            'reference' => 'XS-TEST-MP', 'package_id' => $package->id,
            'status' => OrderStatus::AwaitingPayment, 'customer_name' => 'Ana',
            'customer_email' => 'ana@example.com', 'customer_whatsapp' => '5219991234567',
            'country' => 'MX', 'business_name' => 'Ana Studio', 'currency' => 'MXN',
            'total_amount' => 2700, 'deposit_amount' => 2700, 'balance_amount' => 0,
        ]);
        $order->payments()->create([
            'provider' => 'mercado_pago', 'provider_reference' => 'PREFERENCE-1',
            'status' => 'pending', 'currency' => 'MXN', 'amount' => 2700,
        ]);
        Http::fake([
            'api.mercadopago.com/v1/payments/987654' => Http::response([
                'id' => 987654,
                'status' => 'approved',
                'external_reference' => 'XS-TEST-MP',
                'transaction_amount' => 2700,
                'currency_id' => 'MXN',
            ]),
        ]);

        $this->get(route('checkout.payment.success', [
            'order' => $order->reference,
            'payment_id' => '987654',
        ]))->assertOk()
            ->assertSee('Pago confirmado')
            ->assertSee('pago total de $2,700 MXN')
            ->assertSee('Un asesor se comunicará contigo')
            ->assertSee('ana@example.com');

        $this->assertSame(OrderStatus::Paid, $order->refresh()->status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'provider' => 'mercado_pago',
            'provider_reference' => '987654',
            'status' => 'approved',
            'payment_type' => 'full',
            'amount' => 2700,
        ]);
    }

    public function test_failed_payment_page_keeps_customer_details_and_offers_retry(): void
    {
        $package = Package::where('slug', 'pagina-profesional')->firstOrFail();
        $order = Order::create([
            'reference' => 'XS-TEST-FAILED', 'package_id' => $package->id,
            'status' => OrderStatus::AwaitingPayment, 'customer_name' => 'María López',
            'customer_email' => 'maria@example.com', 'customer_whatsapp' => '5219991234567',
            'country' => 'MX', 'business_name' => 'Estudio María', 'currency' => 'MXN',
            'total_amount' => 4400, 'deposit_amount' => 4400, 'balance_amount' => 0,
        ]);

        $this->get(route('checkout.payment.failure', $order->reference))
            ->assertOk()
            ->assertSee('Pago no completado')
            ->assertSee('María López')
            ->assertSee('Intentar de nuevo');
    }
}
