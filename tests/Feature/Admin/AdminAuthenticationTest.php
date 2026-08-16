<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_can_view_internal_login(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Bienvenido de vuelta')
            ->assertSee('Acceso interno');
    }

    public function test_admin_can_login_and_session_is_regenerated(): void
    {
        $admin = $this->userWithRole('admin');

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'Secret123!',
            'remember' => '1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->refresh()->last_login_at);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $admin = $this->userWithRole('admin', active: false);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'Secret123!',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_without_internal_role_cannot_login(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Secret123!')]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'Secret123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_seller_cannot_enter_an_admin_only_route(): void
    {
        Route::middleware(['web', 'auth', 'internal', 'role:admin'])
            ->get('/_tests/admin-only', fn () => response('ok'));

        $seller = $this->userWithRole('seller');
        $admin = $this->userWithRole('admin');

        $this->actingAs($seller)->get('/_tests/admin-only')->assertForbidden();
        $this->actingAs($admin)->get('/_tests/admin-only')->assertOk();
    }

    public function test_authenticated_internal_user_can_logout(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
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
}
