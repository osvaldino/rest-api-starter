<?php declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

final class UpdateProjectAction
{
    public function execute(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }
}
