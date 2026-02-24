<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Support\Traits\AppliesSorting;
use Illuminate\Pagination\LengthAwarePaginator;

final class TaskService
{
    use AppliesSorting;

    private const array ALLOWED_SORTS = [
        'created_at',
        'updated_at',
        'title',
        'done',
    ];

    public function list(
        ?string $search,
        ?int $projectId,
        ?int $categoryId,
        ?bool $done,
        string $sort,
        int $perPage,
    ): LengthAwarePaginator {
        $perPage = max(1, min(100, $perPage));

        $query = Task::query();

        if ($search !== null && $search !== '') {
            $query->where('title', 'ilike', "%$search%");
        }

        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        if ($done !== null) {
            $query->where('done', $done);
        }

        $this->applySorting($query, $sort, self::ALLOWED_SORTS);

        return $query->paginate($perPage)->through(fn ($task) => new TaskResource($task));
    }
}
