<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static Builder belongs(User $user)
 * @method static Builder actualFirst
 * @method static Builder ownActualFirst(User $user)
 */
final class Project extends Model
{
    use HasFactory;

    public const TABLE = 'projects';

    public const PROP_ID = 'id';
    public const PROP_USER_ID = 'user_id';
    public const PROP_NAME = 'name';
    public const PROP_UPDATED_AT = Model::UPDATED_AT;

    public const RELATION_TASKS = 'tasks';

    protected $with = [
        self::RELATION_TASKS,
    ];

    protected $fillable = [
        self::PROP_NAME,
        self::PROP_USER_ID,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(
            Task::class,
            Task::PROP_PROJECT_ID,
            self::PROP_ID
        );
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeBelongs(Builder $query, User $user): void
    {
        $query->where(self::PROP_USER_ID, $user->id);
    }

    public function scopeActualFirst(Builder $query): void
    {
        $query->orderByDesc(Project::PROP_UPDATED_AT);
    }

    public function scopeOwnActualFirst(Builder $query, User $user): void
    {
        $this->scopeBelongs($query, $user);
        $this->scopeActualFirst($query);
    }
}
