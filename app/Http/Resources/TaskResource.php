<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
final class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'done' => $this->done,
            //            'created_at' => $this->created_at,
            //            'updated_at' => $this->updated_at,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
