<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index(IndexProjectRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $search = trim($filters['search'] ?? '');

        $projects = Project::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->whereLike('client_name', "%{$search}%")
                    ->orWhereLike('project_name', "%{$search}%"),
            ))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['sort'] ?? null, fn ($query, $sort) => $query->orderBy(
                $sort,
                $filters['direction'] ?? 'asc',
            ))
            ->get();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return (new ProjectResource($project))->response()->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Project $project): Response
    {
        $project->delete();

        return response()->noContent();
    }
}
