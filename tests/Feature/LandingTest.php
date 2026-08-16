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

    public function test_seeded_homepage_contains_the_store_quote_form(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('quote.store', ['package' => 'tienda-en-linea']), false);
    }
}
