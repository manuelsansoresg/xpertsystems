<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class PackageModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_list_packages(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertOk()
            ->assertSee('Paquetes');
    }

    public function test_admin_can_create_package(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Test Package',
            'slug' => 'test-package',
            'short_description' => 'Test description',
            'price_type' => 'fixed',
            'price' => 1000,
            'currency' => 'MXN',
            'active' => true,
            'public_visibility' => true,
            'features' => [
                ['title' => 'Feature 1', 'visible_summary' => true],
                ['title' => 'Feature 2', 'visible_summary' => false],
            ],
        ]);

        $response->assertRedirect(route('admin.packages.index'));

        $package = Package::query()->where('slug', 'test-package')->first();
        $this->assertNotNull($package);
        $this->assertEquals('Test Package', $package->name);
        $this->assertCount(2, $package->featureItems);
    }

    public function test_slug_must_be_unique(): void
    {
        $admin = $this->userWithRole('admin');
        Package::create(['name' => 'Existing', 'slug' => 'existing-slug', 'short_description' => 'Test', 'price_type' => 'fixed', 'price' => 100, 'active' => true]);

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Another',
            'slug' => 'existing-slug',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 200,
        ])->assertSessionHasErrors('slug');
    }

    public function test_fixed_requires_price(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'No Price',
            'slug' => 'no-price',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => null,
        ])->assertSessionHasErrors('price');
    }

    public function test_quote_allows_null_price(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Quote Package',
            'slug' => 'quote-package',
            'short_description' => 'Test',
            'price_type' => 'quote',
            'price' => null,
        ])->assertRedirect(route('admin.packages.index'));
    }

    public function test_renewal_enabled_requires_price(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Renewal Package',
            'slug' => 'renewal-package',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 1000,
            'renewal_enabled' => true,
            'renewal_price' => null,
        ])->assertSessionHasErrors('renewal_price');
    }

    public function test_inactive_package_not_visible_publicly(): void
    {
        Package::create([
            'name' => 'Inactive',
            'slug' => 'inactive',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 100,
            'active' => false,
            'public_visibility' => true,
        ]);

        $this->get(route('home'))->assertDontSee('Inactive');
    }

    public function test_sort_order_controls_public_order(): void
    {
        Package::create(['name' => 'Second', 'slug' => 'second', 'short_description' => 'Test', 'price_type' => 'fixed', 'price' => 200, 'sort_order' => 2, 'active' => true]);
        Package::create(['name' => 'First', 'slug' => 'first', 'short_description' => 'Test', 'price_type' => 'fixed', 'price' => 100, 'sort_order' => 1, 'active' => true]);

        $response = $this->get(route('home'));
        $response->assertSeeInOrder(['First', 'Second']);
    }

    public function test_featured_package_gets_featured_class(): void
    {
        Package::create([
            'name' => 'Featured',
            'slug' => 'featured',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 100,
            'is_featured' => true,
            'active' => true,
        ]);

        $this->get(route('home'))->assertSee('pkg--featured');
    }

    public function test_features_saved_correctly(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Features Test',
            'slug' => 'features-test',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 100,
            'features' => [
                ['title' => 'Visible Feature', 'visible_summary' => true],
                ['title' => 'Hidden Feature', 'visible_summary' => false],
            ],
        ]);

        $package = Package::query()->where('slug', 'features-test')->first();
        $summaryFeatures = $package->featureItems->where('visible_summary', true);
        $detailFeatures = $package->featureItems->where('visible_summary', false);

        $this->assertCount(1, $summaryFeatures);
        $this->assertCount(1, $detailFeatures);
    }

    public function test_renewal_appears_when_enabled(): void
    {
        Package::create([
            'name' => 'Renewal Test',
            'slug' => 'renewal-test',
            'short_description' => 'Test',
            'price_type' => 'fixed',
            'price' => 1000,
            'renewal_enabled' => true,
            'renewal_price' => 500,
            'show_renewal_price' => true,
            'active' => true,
        ]);

        $this->get(route('home'))->assertSee('Renovación anual');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('Secret123!'),
            'active' => true,
        ]);
        $user->roles()->attach(Role::query()->where('slug', $role)->firstOrFail());

        return $user;
    }
}
