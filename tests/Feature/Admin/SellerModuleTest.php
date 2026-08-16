<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class SellerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_list_sellers(): void
    {
        $admin = $this->userWithRole('admin');
        $this->createSeller('SELLER01');

        $this->actingAs($admin)
            ->get(route('admin.sellers.index'))
            ->assertOk()
            ->assertSee('Equipo comercial');
    }

    public function test_admin_can_create_seller(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Carlos Pérez',
            'last_name' => 'Pérez',
            'email' => 'carlos@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'CARLOS7A2',
            'commission_type' => 'percentage',
            'commission_value' => 20,
            'active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $seller = User::query()->where('email', 'carlos@example.com')->first();
        $this->assertNotNull($seller);
        $this->assertTrue($seller->hasRole('seller'));
        $this->assertNotNull($seller->sellerProfile);
        $this->assertEquals('CARLOS7A2', $seller->sellerProfile->referral_code);
    }

    public function test_admin_can_view_seller_profile(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller('PROFILE01');

        $this->actingAs($admin)
            ->get(route('admin.sellers.show', $seller->sellerProfile))
            ->assertOk()
            ->assertSee('PROFILE01')
            ->assertSee('Configuración');
    }

    public function test_admin_can_edit_seller(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller('EDITME01');

        $this->actingAs($admin)->put(route('admin.sellers.update', $seller->sellerProfile), [
            'name' => 'Carlos Updated',
            'email' => $seller->email,
            'referral_code' => 'UPDATED01',
            'commission_type' => 'fixed',
            'commission_value' => 500,
            'active' => true,
        ])->assertRedirect(route('admin.sellers.index'));

        $seller->refresh();
        $this->assertEquals('Carlos Updated', $seller->name);
        $this->assertEquals('UPDATED01', $seller->sellerProfile->referral_code);
    }

    public function test_admin_can_deactivate_seller(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller('DEACT01');

        $this->assertTrue($seller->active);

        $this->actingAs($admin)
            ->patch(route('admin.sellers.toggle', $seller->sellerProfile))
            ->assertRedirect(route('admin.sellers.index'));

        $this->assertFalse($seller->refresh()->active);
    }

    public function test_seller_cannot_access_admin_sellers(): void
    {
        $seller = $this->createSeller('SELLER02');

        $this->actingAs($seller)
            ->get(route('admin.sellers.index'))
            ->assertForbidden();
    }

    public function test_referral_code_must_be_unique(): void
    {
        $admin = $this->userWithRole('admin');
        $this->createSeller('UNIQUE01');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Another Seller',
            'email' => 'another@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'UNIQUE01',
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ])->assertSessionHasErrors('referral_code');
    }

    public function test_email_must_be_unique(): void
    {
        $admin = $this->userWithRole('admin');
        $existingSeller = $this->createSeller('EMAIL01');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Another Seller',
            'email' => $existingSeller->email,
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'NEWCODE1',
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ])->assertSessionHasErrors('email');
    }

    public function test_percentage_cannot_exceed_100(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Bad Seller',
            'email' => 'bad@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'BADCODE1',
            'commission_type' => 'percentage',
            'commission_value' => 150,
        ])->assertSessionHasErrors('commission_value');
    }

    public function test_create_seller_creates_user_and_profile(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test Seller',
            'email' => 'test@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'TEST0001',
            'commission_type' => 'percentage',
            'commission_value' => 15,
        ]);

        $user = User::query()->where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('seller'));
        $this->assertNotNull($user->sellerProfile);
        $this->assertEquals($user->id, $user->sellerProfile->user_id);
    }

    public function test_inactive_seller_cannot_login(): void
    {
        $seller = $this->createSeller('INACTIVE', active: false);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $seller->email,
            'password' => 'Secret123!',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_seller_can_access_own_dashboard(): void
    {
        $seller = $this->createSeller('DASH01');

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('DASH01');
    }

    private function userWithRole(string $role, bool $active = true): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('Secret123!'),
            'active' => $active,
        ]);
        $user->roles()->attach(Role::query()->where('slug', $role)->firstOrFail());

        return $user;
    }

    private function createSeller(string $referralCode = 'TESTCODE', bool $active = true): User
    {
        $seller = User::factory()->create([
            'password' => Hash::make('Secret123!'),
            'active' => $active,
        ]);
        $seller->roles()->attach(Role::query()->where('slug', 'seller')->firstOrFail());

        SellerProfile::create([
            'user_id' => $seller->id,
            'referral_code' => $referralCode,
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ]);

        return $seller;
    }
}
