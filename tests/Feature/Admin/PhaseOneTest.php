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

final class PhaseOneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_seller_cannot_access_admin_crud_routes(): void
    {
        $seller = $this->userWithRole('seller');

        $this->actingAs($seller)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($seller)
            ->get(route('admin.sellers.index'))
            ->assertForbidden();
    }

    public function test_seller_can_access_seller_dashboard(): void
    {
        $seller = $this->createSeller();

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Hola');
    }

    public function test_admin_cannot_access_seller_dashboard(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('seller.dashboard'))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->userWithRole('admin', active: false);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'Secret123!',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_seller_login_redirects_to_seller_dashboard(): void
    {
        $seller = $this->createSeller();

        $this->post(route('admin.login.store'), [
            'email' => $seller->email,
            'password' => 'Secret123!',
        ])->assertRedirect(route('seller.dashboard'));
    }

    public function test_admin_can_create_seller(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'MARIA7F2A',
            'commission_type' => 'percentage',
            'commission_value' => 20.00,
            'active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $seller = User::query()->where('email', 'maria@example.com')->first();
        $this->assertNotNull($seller);
        $this->assertTrue($seller->hasRole('seller'));
        $this->assertNotNull($seller->sellerProfile);
        $this->assertEquals('MARIA7F2A', $seller->sellerProfile->referral_code);
        $this->assertEquals('percentage', $seller->sellerProfile->commission_type);
        $this->assertEquals(20.00, (float) $seller->sellerProfile->commission_value);
    }

    public function test_referral_code_must_be_unique(): void
    {
        $admin = $this->userWithRole('admin');
        $this->createSeller(referralCode: 'UNIQUE01');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test Seller',
            'email' => 'test@example.com',
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
        $this->userWithRole('seller');

        $existingUser = User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))->first();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Another Seller',
            'email' => $existingUser->email,
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'role' => 'seller',
            'referral_code' => 'NEWCODE1',
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ])->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = $this->userWithRole('admin');
        $this->userWithRole('seller');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Usuarios');
    }

    public function test_admin_can_toggle_user_active(): void
    {
        $admin = $this->userWithRole('admin');
        $otherUser = $this->userWithRole('seller');

        $this->assertTrue($otherUser->active);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $otherUser))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($otherUser->refresh()->active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertTrue($admin->refresh()->active);
    }

    public function test_seller_can_see_referral_code(): void
    {
        $seller = $this->createSeller(referralCode: 'SELLER123');

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('SELLER123');
    }

    public function test_admin_can_edit_seller(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller(referralCode: 'EDITME01');

        $this->actingAs($admin)->put(route('admin.sellers.update', $seller->sellerProfile), [
            'name' => 'Updated Name',
            'email' => $seller->email,
            'referral_code' => 'UPDATED01',
            'commission_type' => 'fixed',
            'commission_value' => 500,
            'active' => true,
        ])->assertRedirect(route('admin.sellers.index'));

        $seller->refresh();
        $this->assertEquals('Updated Name', $seller->name);
        $this->assertEquals('UPDATED01', $seller->sellerProfile->referral_code);
        $this->assertEquals('fixed', $seller->sellerProfile->commission_type);
    }

    public function test_admin_can_deactivate_seller(): void
    {
        $admin = $this->userWithRole('admin');
        $seller = $this->createSeller();

        $this->assertTrue($seller->active);

        $this->actingAs($admin)
            ->patch(route('admin.sellers.toggle', $seller->sellerProfile))
            ->assertRedirect(route('admin.sellers.index'));

        $this->assertFalse($seller->refresh()->active);
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

    private function createSeller(string $referralCode = 'TESTCODE'): User
    {
        $seller = User::factory()->create([
            'password' => Hash::make('Secret123!'),
            'active' => true,
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
