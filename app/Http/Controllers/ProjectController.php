<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    
    public function index()
    {
        return inertia('Project/Index');
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
        //
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
