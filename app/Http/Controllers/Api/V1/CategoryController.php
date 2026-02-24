<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Categories\CreateCategoryAction;
use App\Actions\Categories\DeleteCategoryAction;
use App\Actions\Categories\UpdateCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CreateCategoryAction $createCategoryAction,
        private readonly UpdateCategoryAction $updateCategoryAction,
        private readonly DeleteCategoryAction $deleteCategoryAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->list(
            search: $request->query('search'),
            sort: $request->query('sort', '-created_at'),
            perPage: (int) $request->query('per_page', '15'),
        );

        return ApiResponse::paginated($categories);
    }

    public function show(Category $category): JsonResponse
    {
        return ApiResponse::success(
            data: new CategoryResource($category),
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->createCategoryAction->execute($request->validated());

        return ApiResponse::success(
            data: new CategoryResource($category),
            message: 'Category created successfully.',
            statusCode: 201,
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->updateCategoryAction->execute($category, $request->validated());

        return ApiResponse::success(
            data: new CategoryResource($category),
            message: 'Category updated successfully.',
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->deleteCategoryAction->execute($category);

        return ApiResponse::success(
            message: 'Category deleted successfully.',
        );
    }
}
