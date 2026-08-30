<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_internal_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_displays_global_commercial_metrics(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->userWithRole('admin');
        $package = Package::query()->where('slug', 'landing-page')->firstOrFail();
        $this->createOrder($package, 'Cliente Global');

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('El negocio, en perspectiva')
            ->assertSee('Cliente Global')
            ->assertSee('$1,900', false);
    }

    public function test_seller_dashboard_only_displays_attributed_orders(): void
    {
        $this->seed(DatabaseSeeder::class);
        $seller = $this->userWithRole('seller');
        $otherSeller = $this->userWithRole('seller');
        $package = Package::query()->where('slug', 'pagina-profesional')->firstOrFail();

        $this->createOrder($package, 'Cliente Propio', $seller);
        $this->createOrder($package, 'Cliente Ajeno', $otherSeller);

        $this->actingAs($seller)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Tu trabajo, en números')
            ->assertSee('Cliente Propio')
            ->assertDontSee('Cliente Ajeno');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', $role)->firstOrFail());

        return $user;
    }

    private function createOrder(Package $package, string $customer, ?User $seller = null): Order
    {
        return Order::query()->create([
            'reference' => 'XS-'.str()->upper(str()->random(10)),
            'package_id' => $package->id,
            'seller_id' => $seller?->id,
            'status' => OrderStatus::AwaitingPayment,
            'payment_status' => 'pending',
            'customer_name' => $customer,
            'customer_email' => str()->slug($customer).'@example.com',
            'customer_whatsapp' => '9991234567',
            'country' => 'MX',
            'business_name' => $customer,
            'currency' => 'MXN',
            'package_name_snapshot' => $package->name,
            'subtotal_amount' => $package->price,
            'total_amount' => $package->price,
            'deposit_amount' => $package->deposit_amount ?? 0,
            'balance_amount' => $package->direct_checkout ? 0 : $package->price,
        ]);
    }
}
