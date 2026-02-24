<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppliesSorting
{
    private function applySorting(Builder $query, string $sort, array $allowedSorts): void
    {
        $direction = 'asc';

        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = ltrim($sort, '-');
        }

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);
    }
}
