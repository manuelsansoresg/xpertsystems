<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderCheckoutStarted;
use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([PreventRequestForgery::class]);
        Mail::fake();
    }

    public function test_checkout_form_is_embedded_in_home_and_has_no_standalone_page(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('checkout.show'));

        $this->get(route('home', ['checkout' => 'landing-page']))
            ->assertOk()
            ->assertSee('CHECKOUT')
            ->assertSee('Landing Page')
            ->assertSee('Continuar a Mercado Pago');

        $this->get('/contratar/landing-page')->assertStatus(405);
    }

    public function test_home_direct_checkout_buttons_open_the_embedded_form(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee("@click=\"openCheckout('landing-page')\"", false)
            ->assertSee('Contratar Landing')
            ->assertSee("@click=\"openCheckout('pagina-profesional')\"", false)
            ->assertSee('Contratar Profesional');
    }

    public function test_home_store_button_points_directly_to_whatsapp(): void
    {
        Setting::query()->where('key', 'whatsapp_number')->update(['value' => '5219990001111']);
        $expectedUrl = 'https://wa.me/5219990001111?text='.rawurlencode('Hola, quiero cotizar el paquete Tienda en Línea con XpertSystems.');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.$expectedUrl.'"', false)
            ->assertSee('Cotizar mi tienda')
            ->assertSee('data-package="tienda-en-linea"', false);
    }

    public function test_prices_page_keeps_all_packages_and_their_correct_actions(): void
    {
        Setting::query()->where('key', 'whatsapp_number')->update(['value' => '5219990001111']);
        $landing = Package::where('slug', 'landing-page')->firstOrFail();
        $professional = Package::where('slug', 'pagina-profesional')->firstOrFail();

        $this->get(route('precios'))
            ->assertOk()
            ->assertSee('Landing Page')
            ->assertSee('href="'.route('home', ['checkout' => $landing->slug]).'#paquetes"', false)
            ->assertSee('Página Profesional')
            ->assertSee('href="'.route('home', ['checkout' => $professional->slug]).'#paquetes"', false)
            ->assertSee('Tienda en Línea')
            ->assertSee('href="https://wa.me/5219990001111?', false)
            ->assertSee('Solicitar cotización');
    }

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
        $this->assertSame('2700.00', $order->total_amount);
        $this->assertSame('2700.00', $order->deposit_amount);
        $this->assertSame('0.00', $order->balance_amount);
        $this->assertSame(OrderStatus::AwaitingPayment, $order->status);
        Mail::assertSent(OrderCheckoutStarted::class, function (OrderCheckoutStarted $mail) use ($order): bool {
            return $mail->hasTo('maria@example.com')
                && $mail->order->is($order)
                && $mail->checkoutUrl === 'https://sandbox.mercadopago.com/checkout/pref-123';
        });

        Http::assertSent(function ($request) use ($order): bool {
            return $request->url() === 'https://api.mercadopago.com/checkout/preferences'
                && $request['external_reference'] === $order->reference
                && $request['items'][0]['unit_price'] === 2700.0
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

    public function test_store_package_has_no_checkout_page(): void
    {
        $this->get('/contratar/tienda-en-linea')->assertStatus(405);
    }

    public function test_package_seeder_enables_direct_checkout_for_landing_and_professional(): void
    {
        Package::query()
            ->whereIn('slug', ['landing-page', 'pagina-profesional'])
            ->update(['direct_checkout' => false, 'requires_quote' => true]);

        $this->seed(PackageSeeder::class);

        $this->assertDatabaseHas('packages', [
            'slug' => 'landing-page',
            'price' => 1900,
            'direct_checkout' => true,
            'requires_quote' => false,
        ]);
        $this->assertDatabaseHas('packages', [
            'slug' => 'pagina-profesional',
            'price' => 2700,
            'direct_checkout' => true,
            'requires_quote' => false,
        ]);
        $this->assertSame(1, Package::withTrashed()->where('slug', 'landing-page')->count());
        $this->assertSame(1, Package::withTrashed()->where('slug', 'pagina-profesional')->count());
    }

    public function test_package_seeder_keeps_store_as_quote_only(): void
    {
        Package::query()
            ->where('slug', 'tienda-en-linea')
            ->update(['price' => 1, 'direct_checkout' => true, 'requires_quote' => false]);

        $this->seed(PackageSeeder::class);

        $this->assertDatabaseHas('packages', [
            'slug' => 'tienda-en-linea',
            'price' => 3500,
            'direct_checkout' => false,
            'requires_quote' => true,
        ]);
        $this->assertSame(1, Package::withTrashed()->where('slug', 'tienda-en-linea')->count());
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

    public function test_quote_creates_lead_and_redirects_to_whatsapp(): void
    {
        config(['xpertsystems.whatsapp_number' => '5219990001111']);
        $package = Package::where('slug', 'tienda-en-linea')->firstOrFail();

        $response = $this->post(route('quote.store', $package), [
            'name' => 'Carlos Gómez',
            'email' => 'carlos@example.com',
            'whatsapp' => '+52 999 555 1234',
            'business_name' => 'Tienda Carlos',
            'message' => 'Quiero vender zapatos',
        ]);

        $this->assertDatabaseHas('leads', [
            'email' => 'carlos@example.com',
            'package_id' => $package->id,
            'source' => 'store_quote',
        ]);

        $response->assertRedirect();
        $target = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/5219990001111', $target);
        $this->assertStringContainsString('Carlos', urldecode($target));
        $this->assertStringContainsString('Tienda Carlos', urldecode($target));
    }

    public function test_quote_works_without_business_name(): void
    {
        config(['xpertsystems.whatsapp_number' => '5219990001111']);
        $package = Package::where('slug', 'tienda-en-linea')->firstOrFail();

        $response = $this->post(route('quote.store', $package), [
            'name' => 'Carlos Gómez',
            'email' => 'carlos@example.com',
            'whatsapp' => '+52 999 555 1234',
        ]);

        $this->assertDatabaseHas('leads', [
            'email' => 'carlos@example.com',
            'business_name' => null,
            'source' => 'store_quote',
        ]);

        $target = $response->headers->get('Location');
        $decoded = urldecode($target);
        $this->assertStringContainsString('Soy Carlos Gómez y quisiera', $decoded);
        $this->assertStringNotContainsString('de  y', $decoded);
    }

    public function test_lead_and_order_persist_when_mercado_pago_fails(): void
    {
        config(['services.mercado_pago.access_token' => 'test-token']);
        Http::fake([
            'api.mercadopago.com/checkout/preferences' => Http::response([], 500),
        ]);
        $package = Package::where('slug', 'landing-page')->firstOrFail();

        $response = $this->post(route('checkout.store', $package), [
            'name' => 'Ana Ruiz',
            'email' => 'ana@example.com',
            'whatsapp' => '+52 999 111 2233',
            'country' => 'MX',
            'business_name' => 'Ana Studio',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('home', ['checkout' => $package->slug]).'#paquetes');
        $response->assertSessionHas('payment_error');

        $this->assertDatabaseHas('leads', ['email' => 'ana@example.com']);
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'ana@example.com',
            'status' => OrderStatus::AwaitingPayment->value,
            'total_amount' => 1900,
        ]);
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
            'total_amount' => 1900, 'deposit_amount' => 1900, 'balance_amount' => 0,
        ]);
        $order->payments()->create([
            'provider' => 'mercado_pago', 'provider_reference' => 'PREFERENCE-WEBHOOK',
            'status' => 'pending', 'currency' => 'MXN', 'amount' => 1900,
        ]);
        Http::fake([
            'api.mercadopago.com/v1/payments/123456' => Http::response([
                'id' => 123456, 'status' => 'approved',
                'external_reference' => 'XS-TEST-WEBHOOK',
                'transaction_amount' => 1900, 'currency_id' => 'MXN',
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
            'total_amount' => 1900, 'deposit_amount' => 1900, 'balance_amount' => 0,
        ]);
        $order->payments()->create([
            'provider' => 'mercado_pago', 'provider_reference' => 'PREFERENCE-1',
            'status' => 'pending', 'currency' => 'MXN', 'amount' => 1900,
        ]);
        Http::fake([
            'api.mercadopago.com/v1/payments/987654' => Http::response([
                'id' => 987654,
                'status' => 'approved',
                'external_reference' => 'XS-TEST-MP',
                'transaction_amount' => 1900,
                'currency_id' => 'MXN',
            ]),
        ]);

        $this->get(route('checkout.payment.success', [
            'order' => $order->reference,
            'payment_id' => '987654',
        ]))->assertOk()
            ->assertSee('Pago confirmado')
            ->assertSee('pago total de $1,900 MXN')
            ->assertSee('Un asesor se comunicará contigo')
            ->assertSee('ana@example.com');

        $this->assertSame(OrderStatus::Paid, $order->refresh()->status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'provider' => 'mercado_pago',
            'provider_reference' => '987654',
            'status' => 'approved',
            'payment_type' => 'full',
            'amount' => 1900,
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
            'total_amount' => 2700, 'deposit_amount' => 2700, 'balance_amount' => 0,
        ]);

        $this->get(route('checkout.payment.failure', $order->reference))
            ->assertOk()
            ->assertSee('Pago no completado')
            ->assertSee('María López')
            ->assertSee('Intentar de nuevo');
    }
}
