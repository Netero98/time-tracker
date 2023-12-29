<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TrackRequest;
use App\Models\Project;
use App\Models\Track;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function store(TrackRequest $request, int $projectId)
    {
        /**
         * @var Project $project
         */
        $project = Project::belongs($request->user())->whereKey($projectId)->firstOrFail();

        $project->tracks()->create($request->validated());
    }

    public function update(TrackRequest $request, int $id)
    {
        $validated = $request->validated();

        $track = Track::query()->whereKey($id)->belongs($request->user())->firstOrFail();

        $track->update($validated);

        return to_route(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
    }

    public function destroy(Request $request, int $id)
    {
        $track = Track::belongs($request->user())->whereKey($id)->firstOrFail();
        $track->delete();
    }
}
