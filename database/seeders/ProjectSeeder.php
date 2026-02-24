<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory(20)->create();
    }
}
