<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        $projects = Project::latest()->get();
        $users = User::where('is_active', true)->get();

        return inertia('Task/Create', compact('projects', 'users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'title' => 'required|string',
            'description' => 'required',
            'department' => 'required|string',
            'participants' => 'required|array|min:1',
            'priority' => 'required|string',
            'reminder' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'media' => 'nullable',
        ]);

        $task = Task::create($request->except('participants') + ['user_id' => auth()->id()]);

        foreach ($request->participants as $user_id) {
            // Adjuntar el usuario a la tarea
            $task->participants()->attach($user_id);
        }

        // event(new RecordCreated($task));

        $task->addAllMediaFromRequest('media')->each(fn ($file) => $file->toMediaCollection('files'));

        return to_route('projects.show', ['project' => $request->project_id]);
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
        // return $request->priority;
        $validated = $request->validate([
            'status' => 'required|string',
            'description' => 'required',
            'department' => 'required|string',
            'priority' => 'nullable|string',
        ]);

        $task->update($validated);

        if ($request->comment) {
            $comment = new Comment([
                'body' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $task->comments()->save($comment);
        }

        // Obtén el proyecto al que pertenece la tarea
        $project_id = $task->project_id;
        $project = Project::with('tasks')->find($project_id);

        // Verifica si todas las tareas del proyecto están terminadas
        $allTasksCompleted = $project->tasks->where('status', 'Terminada')->count() === $project->tasks->count();

        if ($allTasksCompleted) {
            // Establece la fecha finished_at en la fecha actual
            $project->finished_at = now();
            $project->save();
        }
        // event(new RecordEdited($task));

        return response()->json(['item' => TaskResource::make($task->fresh(['participants', 'project', 'user', 'comments.user', 'media']))]);
    }


    public function destroy(Task $task)
    {
        //
    }


    public function comment(Request $request, Task $task)
    {

        $comment = new Comment([
            'body' => $request->comment,
            'user_id' => auth()->id(),
        ]);
        $task->comments()->save($comment);
        // event(new RecordCreated($comment)); me dice que el id del usuario no tiene un valor por default.
        return to_route('projects.show', ['project' => $request->project_id]);
        // return response()->json(['item' => $comment]);
    }

    public function pausePlayTask(Task $task)
    {
        if ($task->is_paused) {
            $task->update([
                'is_paused' => false
            ]);
        } else {
            $task->update([
                'is_paused' => true
            ]);
        }
        $task->save();
        // return response()->json(['item' => $comment]);
    }

    public function updateStatus(Task $task, Request $request)
    {
        $task->update(['status' => $request->status]);

        return response()->json([]);
    }

    public function getLateTasks()
    {
        $late_tasks = Task::with(['participants', 'project'])->where('status', '!=', 'Terminada')->whereDate('end_date', '<', today())->get();
        
        $currentDate = today();

        $late_tasks = $late_tasks->map(function ($task) use ($currentDate) {
            $lateDays = $task->end_date->diffInDays($currentDate); // Calcula la diferencia en días entre end_date y la fecha actual
            $task['late_days'] = $lateDays; // Agrega la propiedad "late_days" al objeto de la tarea
            return $task;
        });

        return response()->json(['items' => $late_tasks]);
    }
}
