<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectWithStatisticsResource;
use App\Models\Project;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Projects', [
            'projects' => ProjectWithStatisticsResource::collection(
                Project::ownActualFirst($request->user())->get()
            )->toArray($request)
        ]);
    }

    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        $validated[Project::PROP_USER_ID] = $request->user()->id;

        $project = new Project($validated);
        $project->save();

        return to_route(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
    }

    public function update(ProjectRequest $request, int $id)
    {
        $validated = $request->validated();

        $project = Project::belongs($request->user())
            ->whereKey($id)
            ->firstOrFail();

        $project->update($validated);

        to_route(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
    }

    public function destroy(Request $request, int $id)
    {
        $project = Project::belongs($request->user())
            ->whereKey($id)
            ->firstOrFail();

        $project->delete();

        to_route(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
    }
}
