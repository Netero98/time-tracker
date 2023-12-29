<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(TaskRequest $request, int $projectId)
    {
        /**
         * @var Project $project
         */
        $project = Project::belongs($request->user())->whereKey($projectId)->firstOrFail();

        $project->tasks()->create($request->validated());
    }

    public function update(TaskRequest $request, int $id)
    {
        $validated = $request->validated();

        $task = Task::query()->whereKey($id)->belongs($request->user())->firstOrFail();

        $task->update($validated);

        return to_route(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
    }

    public function destroy(Request $request, int $id)
    {
        $task = Task::belongs($request->user())->whereKey($id)->firstOrFail();
        $task->delete();
    }
}
