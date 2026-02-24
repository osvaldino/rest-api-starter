<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;

final class UpdateTaskAction
{
    public function execute(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }
}
