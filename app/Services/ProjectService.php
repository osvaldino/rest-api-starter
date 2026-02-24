<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Support\Traits\AppliesSorting;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProjectService
{
    use AppliesSorting;

    private const array ALLOWED_SORTS = [
        'created_at',
        'updated_at',
        'name',
        'status',
    ];

    public function list(
        ?string $search,
        ?string $status,
        string $sort,
        int $perPage,
    ): LengthAwarePaginator {
        $perPage = max(1, min(100, $perPage));

        $query = Project::query();

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'ilike', "%$search%")
                    ->orWhere('description', 'ilike', "%$search%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $this->applySorting($query, $sort, self::ALLOWED_SORTS);

        return $query->paginate($perPage)->through(fn ($project) => new ProjectResource($project));
    }
}
