<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

final class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $categories = Category::all();

        Task::factory(50)
            ->sequence(fn () => [
                'project_id' => $projects->random()->id,
                'category_id' => fake()->boolean(50) ? $categories->random()->id : null,
            ])
            ->create();
    }
}
