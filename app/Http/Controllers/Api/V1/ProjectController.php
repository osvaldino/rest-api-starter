<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Projects\CreateProjectAction;
use App\Actions\Projects\DeleteProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly CreateProjectAction $createProjectAction,
        private readonly UpdateProjectAction $updateProjectAction,
        private readonly DeleteProjectAction $deleteProjectAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->list(
            search: $request->query('search'),
            status: $request->query('status'),
            sort: $request->query('sort', '-created_at'),
            perPage: (int) $request->query('per_page', '15'),
        );

        return ApiResponse::paginated($projects);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load('tasks');

        return ApiResponse::success(
            data: new ProjectResource($project),
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->createProjectAction->execute($request->validated());

        return ApiResponse::success(
            data: new ProjectResource($project),
            message: 'Project created successfully.',
            statusCode: 201,
        );
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->updateProjectAction->execute($project, $request->validated());

        return ApiResponse::success(
            data: new ProjectResource($project),
            message: 'Project updated successfully.',
        );
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->deleteProjectAction->execute($project);

        return ApiResponse::success(
            message: 'Project deleted successfully.',
        );
    }
}
