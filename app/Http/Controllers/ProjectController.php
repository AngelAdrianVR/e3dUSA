<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
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
        //event(new RecordCreated($project)); evento para registro creado
    }

    
    public function show($project_id)
    {

        $project = ProjectResource::make(Project::with(['tasks' => ['participants', 'project', 'user']])->find($project_id));
        $projects = ProjectResource::collection(Project::with(['tasks' => ['participants', 'project', 'user', 'comments.user', 'media']])->latest()->get());
        $users = User::all();

        // return $project;
        return inertia('Project/Show', compact('project', 'projects', 'users'));
    }

    
    public function edit(Project $project)
    {
        //
    }

    
    public function update(Request $request, Project $project)
    {
        //event(new RecordEdited($project));
    }

    
    public function destroy(Project $project)
    {
        //
    }
}
