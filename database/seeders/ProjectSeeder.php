<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['name' => 'Ramcen', 'accent' => '#E0C287'],
            ['name' => 'Drooopy', 'accent' => '#71A6B8'],
            ['name' => 'Invitatorio', 'accent' => '#D39BA6'],
            ['name' => 'Spa Rosas y Girasoles', 'accent' => '#D7A967'],
            ['name' => 'Kaax Club', 'accent' => '#6E9A75'],
            ['name' => 'Enterwork', 'accent' => '#7B91C1'],
            ['name' => 'UFC Gym', 'accent' => '#C75E52'],
            ['name' => 'Luis Gantús', 'accent' => '#B8A98B'],
        ];

        foreach ($projects as $index => $project) {
            Project::updateOrCreate(
                ['slug' => Str::slug($project['name'])],
                [...$project, 'sort_order' => $index + 1, 'active' => true]
            );
        }
    }
}
