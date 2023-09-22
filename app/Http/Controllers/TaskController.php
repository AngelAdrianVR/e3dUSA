<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    
    public function index()
    {
        //
    }

   
    public function create()
    {
        $projects = Project::latest()->get();
        $users = User::all();

        return inertia('Task/Create', compact('projects', 'users'));
    }

    
    public function store(Request $request)
    {
        //
    }

   
    public function show(Task $task)
    {
        //
    }

    
    public function edit(Task $task)
    {
        //
    }

    
    public function update(Request $request, Task $task)
    {
        //
    }

    
    public function destroy(Task $task)
    {
        //
    }
}
