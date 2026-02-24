<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Models\Project;

final class CreateProjectAction
{
    public function execute(array $data): Project
    {
        return Project::create($data);
    }
}
