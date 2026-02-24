<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Tasks\CreateTaskAction;
use App\Actions\Tasks\DeleteTaskAction;
use App\Actions\Tasks\UpdateTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly CreateTaskAction $createTaskAction,
        private readonly UpdateTaskAction $updateTaskAction,
        private readonly DeleteTaskAction $deleteTaskAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tasks = $this->taskService->list(
            search: $request->query('search'),
            projectId: $request->query('project_id') !== null ? (int) $request->query('project_id') : null,
            categoryId: $request->query('category_id') !== null ? (int) $request->query('category_id') : null,
            done: $request->query('done') !== null ? filter_var($request->query('done'), FILTER_VALIDATE_BOOLEAN) : null,
            sort: $request->query('sort', '-created_at'),
            perPage: (int) $request->query('per_page', '15'),
        );

        return ApiResponse::paginated($tasks);
    }

    public function show(Task $task): JsonResponse
    {
        $task->load(['project', 'category']);

        return ApiResponse::success(
            data: new TaskResource($task),
        );
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->createTaskAction->execute($request->validated());

        return ApiResponse::success(
            data: new TaskResource($task),
            message: 'Task created successfully.',
            statusCode: 201,
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->updateTaskAction->execute($task, $request->validated());

        return ApiResponse::success(
            data: new TaskResource($task),
            message: 'Task updated successfully.',
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->deleteTaskAction->execute($task);

        return ApiResponse::success(
            message: 'Task deleted successfully.',
        );
    }
}
