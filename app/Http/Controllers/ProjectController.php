<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    
    public function index()
    {
        $projects = ProjectResource::collection(Project::with('tasks')->latest()->get());

        // return $projects;
        return inertia('Project/Index', compact('projects'));
    }

    
    public function create()
    {
        return inertia('Project/Create');
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Project $project)
    {

        $projects = ProjectResource::collection(Project::with('tasks.users')->latest()->get());

        // return $projects;
        return inertia('Project/Show', compact('project', 'projects'));
    }

    
    public function edit(Project $project)
    {
        //
    }

    
    public function update(Request $request, Project $project)
    {
        //
    }

    
    public function destroy(Project $project)
    {
        //
    }
}
