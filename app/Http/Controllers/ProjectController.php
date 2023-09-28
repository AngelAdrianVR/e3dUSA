<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    
    public function index()
    {
        $projects = ProjectResource::collection(Project::with('tasks')->latest()->paginate(30));

        // return $projects;
        return inertia('Project/Index', compact('projects'));
    }

    public function dashboard()
    {
        return inertia('Project/Dashboard');
    }
    
    public function create()
    {
        $companies = Company::with('companyBranches.sales')->latest()->get();
        $users = User::where('is_active', true)->get();

        // return $users;
        return inertia('Project/Create', compact('companies', 'users'));
    }

    
    public function store(Request $request)
    {
        
       $validated = $request->validate([
            'project_name' => 'required|string',
            'group' => 'required|string',
            'shipping_address' => 'nullable|string',
            'currency' => 'nullable|string',
            'sat_method' => 'nullable|string',
            'description' => 'required',
            'is_strict_project' => 'boolean',
            'is_internal_project' => 'boolean',
            'budget' => 'nullable|numeric|min:0',
            'type_access_project' => 'required|string',
            'start_date' => 'required',
            'limit_date' => 'required',
            'company_id' => $request->is_internal_project ? 'nullable' : 'required',
            'company_branch_id' => $request->is_internal_project ? 'nullable' : 'required',
            'sale_id' => $request->is_internal_project ? 'nullable' : 'required',
        ]);

            Project::create($validated + ['user_id' => auth()->id()]);
        //event(new RecordCreated($project)); evento para registro creado

        return to_route('projects.index');
    }

    
    public function show($project_id)
    {

        $project = ProjectResource::make(Project::with(['tasks' => ['participants', 'project', 'user']])->find($project_id));
        $projects = ProjectResource::collection(Project::with(['tasks' => ['participants', 'project', 'user', 'comments.user', 'media'], 'user', 'company'])->latest()->get());
        $users = User::all();

        // return $projects;
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
