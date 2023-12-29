<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static Builder belongs (User $user)
 * @see Task::belongs(User $user)
 */
class Task extends Model
{
    public const PROP_PROJECT_ID = 'project_id';
    public const PROP_NAME = 'name';
    public const PROP_SECONDS = 'seconds';

    public const RELATION_PROJECT = 'project';

    protected $fillable = [
        self::PROP_NAME,
        self::PROP_SECONDS,
    ];

    use HasFactory;

    public function project(): HasOne
    {
        return $this->hasOne(
            Project::class,
            Project::PROP_ID,
            self::PROP_PROJECT_ID
        );
    }

    protected static function scopeBelongs(Builder $query, User $user): void
    {
        $query->whereRelation(
            self::RELATION_PROJECT,
            function (Builder $builder) use ($user) {
                $builder->where(Project::PROP_USER_ID, 1);
            }
        );
    }

}
