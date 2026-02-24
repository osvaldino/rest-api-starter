<?php declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

final class CategoryService
{
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
            $query->where('name', 'ilike', "%{$search}%");
        }

        $direction = 'asc';

        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = ltrim($sort, '-');
        }

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'created_at';
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        return $query->paginate($perPage)->through(fn ($category) => new CategoryResource($category));
    }
}
