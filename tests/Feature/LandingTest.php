<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_before_initial_data_is_seeded(): void
    {
        Package::query()->forceDelete();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('class="quote-form"', false);
    }

    public function test_seeded_homepage_contains_checkout_form_and_store_whatsapp_link(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Continuar a Mercado Pago')
            ->assertSee("openCheckout('landing-page')", false)
            ->assertSee("openCheckout('pagina-profesional')", false)
            ->assertSee('data-package="tienda-en-linea"', false)
            ->assertSee('https://wa.me/', false);
    }

    public function test_package_cards_only_show_selected_summary_features(): void
    {
        $package = Package::create([
            'name' => 'Paquete visible',
            'slug' => 'paquete-visible',
            'short_description' => 'Descripción de prueba',
            'price_type' => 'fixed',
            'price' => 1000,
            'active' => true,
            'public_visibility' => true,
        ]);
        $package->featureItems()->createMany([
            ['title' => 'Característica seleccionada única', 'visible_summary' => true, 'sort_order' => 1, 'active' => true],
            ['title' => 'Característica no seleccionada única', 'visible_summary' => false, 'sort_order' => 2, 'active' => true],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Característica seleccionada única')
            ->assertDontSee('Característica no seleccionada única')
            ->assertDontSee('Ver detalles');
    }
}
