<?php declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;

final class CreateTaskAction
{
    public function execute(array $data): Task
    {
        return Task::create($data);
    }
}
