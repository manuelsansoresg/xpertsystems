<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) config('xpertsystems.admin.name'));
        $email = Str::lower(trim((string) config('xpertsystems.admin.email')));
        $password = (string) config('xpertsystems.admin.password');

        if ($name === '' || $email === '' || $password === '') {
            $this->command?->warn('AdminUserSeeder omitido: define ADMIN_NAME, ADMIN_EMAIL y ADMIN_PASSWORD.');

            return;
        }

        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();
        $admin = User::withTrashed()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'first_name' => Str::before($name, ' '),
                'last_name' => Str::contains($name, ' ') ? Str::after($name, ' ') : null,
                'password' => $password,
                'active' => true,
                'email_verified_at' => now(),
                'deleted_at' => null,
            ],
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        $this->command?->info("Administrador interno listo: {$admin->email}");
    }
}
