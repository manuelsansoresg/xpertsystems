<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class CustomerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_list_customers(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Clientes y prospectos');
    }

    public function test_admin_can_create_customer(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'Ana',
            'last_name' => 'López',
            'business_name' => 'Estética Aura',
            'email' => 'ana@example.com',
            'phone' => '+5219991234567',
            'country' => 'MX',
            'currency' => 'MXN',
            'source' => 'referral',
            'status' => 'customer',
            'notes' => 'Interesada en Página Profesional',
        ]);

        $response->assertRedirect(route('admin.customers.index'));

        $customer = Customer::query()->where('email', 'ana@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Ana', $customer->first_name);
        $this->assertEquals('Estética Aura', $customer->business_name);
    }

    public function test_customer_can_exist_without_email(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'Pedro',
            'last_name' => 'Gómez',
            'source' => 'whatsapp',
            'status' => 'lead',
        ])->assertRedirect(route('admin.customers.index'));

        $customer = Customer::query()->where('first_name', 'Pedro')->first();
        $this->assertNotNull($customer);
        $this->assertNull($customer->email);
    }

    public function test_seller_must_have_seller_role(): void
    {
        $admin = $this->userWithRole('admin');
        $adminUser = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'Test',
            'source' => 'direct',
            'status' => 'lead',
            'seller_id' => $adminUser->id,
        ])->assertSessionHasErrors('seller_id');
    }

    public function test_filter_by_seller_works(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller();

        Customer::create([
            'first_name' => 'Cliente',
            'seller_id' => $seller->id,
            'source' => 'referral',
            'status' => 'customer',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['seller_id' => $seller->id]))
            ->assertOk()
            ->assertSee('Cliente');
    }

    public function test_filter_by_status_works(): void
    {
        $admin = $this->userWithRole('admin');

        Customer::create(['first_name' => 'Lead', 'source' => 'direct', 'status' => 'lead']);
        Customer::create(['first_name' => 'Customer', 'source' => 'direct', 'status' => 'customer']);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['status' => 'lead']))
            ->assertOk()
            ->assertSee('Lead')
            ->assertDontSee('Customer');
    }

    public function test_search_by_email_works(): void
    {
        $admin = $this->userWithRole('admin');

        Customer::create(['first_name' => 'Ana', 'email' => 'ana@example.com', 'source' => 'direct', 'status' => 'customer']);
        Customer::create(['first_name' => 'Pedro', 'email' => 'pedro@example.com', 'source' => 'direct', 'status' => 'customer']);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['search' => 'ana@example.com']))
            ->assertOk()
            ->assertSee('Ana')
            ->assertDontSee('Pedro');
    }

    public function test_inactive_customer_remains_in_database(): void
    {
        $admin = $this->userWithRole('admin');

        $customer = Customer::create([
            'first_name' => 'Inactive',
            'source' => 'direct',
            'status' => 'customer',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.customers.toggle', $customer))
            ->assertRedirect(route('admin.customers.index'));

        $customer->refresh();
        $this->assertEquals('inactive', $customer->status);
        $this->assertNotNull(Customer::query()->find($customer->id));
    }

    public function test_seller_id_saved_correctly(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller();

        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'Referido',
            'source' => 'referral',
            'status' => 'customer',
            'seller_id' => $seller->id,
            'referral_code' => 'CARLOS7A2',
        ]);

        $customer = Customer::query()->where('first_name', 'Referido')->first();
        $this->assertEquals($seller->id, $customer->seller_id);
        $this->assertEquals('CARLOS7A2', $customer->referral_code);
    }

    public function test_referral_code_saved_as_snapshot(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller();

        $this->actingAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'Snapshot',
            'source' => 'referral',
            'status' => 'customer',
            'seller_id' => $seller->id,
            'referral_code' => 'HISTORICAL01',
        ]);

        $customer = Customer::query()->where('first_name', 'Snapshot')->first();
        $this->assertEquals('HISTORICAL01', $customer->referral_code);
    }

    public function test_customer_count_in_seller_profile(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller();

        Customer::create([
            'first_name' => 'Cliente',
            'seller_id' => $seller->id,
            'source' => 'referral',
            'status' => 'customer',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sellers.index'))
            ->assertOk()
            ->assertSee('1');
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

    private function createSeller(): User
    {
        $seller = User::factory()->create([
            'password' => Hash::make('Secret123!'),
            'active' => true,
        ]);
        $seller->roles()->attach(Role::query()->where('slug', 'seller')->firstOrFail());

        return $seller;
    }
}
