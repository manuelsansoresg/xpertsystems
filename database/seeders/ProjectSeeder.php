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
            ['name' => 'Drooopy', 'accent' => '#71A6B8'],
            ['name' => 'Ramcen', 'accent' => '#E0C287'],
            ['name' => 'Invitatorio', 'accent' => '#D39BA6'],
            ['name' => 'Rosas & Girasoles', 'accent' => '#D7A967'],
        ];

        foreach ($projects as $index => $project) {
            Project::updateOrCreate(
                ['slug' => Str::slug($project['name'])],
                [...$project, 'sort_order' => $index + 1, 'active' => true]
            );
        }
    }
}