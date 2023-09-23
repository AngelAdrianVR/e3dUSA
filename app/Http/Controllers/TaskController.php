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
        $request->validate([
            'project_id' => 'required',
            'title' => 'required|string',
            'description' => 'required',
            'participants' => 'required|array|min:1',
            'priority' => 'required|string',
            'reminder' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        

        $task = Task::create($request->except('participants') + ['user_id' => auth()->id()]);

        foreach ($request->participants as $user_id) {
            // Adjuntar el usuario a la tarea
            $task->participants()->attach($user_id);
        }
    
        // Resto de tu lógica si es necesario
    
        return to_route('projects.show', ['project'=> $request->project_id]);
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
