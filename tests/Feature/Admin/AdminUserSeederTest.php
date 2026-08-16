<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_uses_configured_environment_credentials(): void
    {
        config([
            'xpertsystems.admin.name' => 'Admin Comercial',
            'xpertsystems.admin.email' => 'admin@example.com',
            'xpertsystems.admin.password' => 'SecurePassword123!',
        ]);

        $this->seed(RoleSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->active);
        $this->assertTrue(Hash::check('SecurePassword123!', $admin->password));
    }
}
