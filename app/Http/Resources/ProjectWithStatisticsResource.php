<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectWithStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Project $project
         */
        $project = $this->resource;

        $tasks = $project->tasks;

        $seconds = 0;

        foreach ($tasks as $task) {
            $seconds += $task->seconds;
        }

        return array_merge(
            parent::toArray($request),
            [
                'seconds_sum' => $seconds,
            ]
        );
    }
}
