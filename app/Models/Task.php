<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $project_id
 * @property int|null $category_id
 * @property string $title
 * @property bool $done
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Project $project
 *
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static Builder<static>|Task newModelQuery()
 * @method static Builder<static>|Task newQuery()
 * @method static Builder<static>|Task onlyTrashed()
 * @method static Builder<static>|Task query()
 * @method static Builder<static>|Task whereCategoryId($value)
 * @method static Builder<static>|Task whereCreatedAt($value)
 * @method static Builder<static>|Task whereDeletedAt($value)
 * @method static Builder<static>|Task whereDone($value)
 * @method static Builder<static>|Task whereId($value)
 * @method static Builder<static>|Task whereProjectId($value)
 * @method static Builder<static>|Task whereTitle($value)
 * @method static Builder<static>|Task whereUpdatedAt($value)
 * @method static Builder<static>|Task withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Task withoutTrashed()
 *
 * @mixin Eloquent
 */
final class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable
        = [
            'project_id',
            'category_id',
            'title',
            'done',
        ];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
