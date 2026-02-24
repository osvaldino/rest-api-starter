<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\Traits\AppliesSorting;
use Illuminate\Pagination\LengthAwarePaginator;

final class CategoryService
{
    use AppliesSorting;

    private const array ALLOWED_SORTS = [
        'created_at',
        'updated_at',
        'name',
    ];

    public function list(
        ?string $search,
        string $sort,
        int $perPage,
    ): LengthAwarePaginator {
        $perPage = max(1, min(100, $perPage));

        $query = Category::query();

        if ($search !== null && $search !== '') {
            $query->where('name', 'ilike', "%$search%");
        }

        $this->applySorting($query, $sort, self::ALLOWED_SORTS);

        return $query->paginate($perPage)->through(fn ($category) => new CategoryResource($category));
    }
}
