<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'category_id' => fake()->boolean(50) ? Category::factory() : null,
            'title' => fake()->sentence(),
            'done' => fake()->boolean(30),
        ];
    }
}
