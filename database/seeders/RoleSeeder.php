<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso total al panel y a la configuración comercial.',
            ],
            [
                'name' => 'Vendedor',
                'slug' => 'seller',
                'description' => 'Acceso limitado a ventas, clientes, cupones y comisiones propias.',
            ],
        ] as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                [...$role, 'active' => true],
            );
        }
    }
}
