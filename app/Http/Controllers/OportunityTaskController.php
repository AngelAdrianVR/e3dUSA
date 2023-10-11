<?php

namespace App\Http\Controllers;

use App\Http\Resources\OportunityTaskResource;
use App\Models\Comment;
use App\Models\OportunityTask;
use App\Models\User;
use Illuminate\Http\Request;

class OportunityTaskController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($oportunity_id)
    {
        $users = User::where('is_active', true)->get();

        return inertia('OportunityTask/Create', compact('users', 'oportunity_id'));
    }

    
    public function store(Request $request, $oportunity_id)
    {
        $request->validate([
            'name' => 'required|string',
            'asigned_id' => 'required',
            'limit_date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|string',
            'description' => 'required',
        ]);

        $oportunity_task = OportunityTask::create($request->except('media') + [
            'user_id' => auth()->id(),
            'oportunity_id' => $oportunity_id,
        ]);

        return to_route('oportunities.show', ['oportunity'=> $oportunity_task->oportunity_id]);
    }
    
    public function show(OportunityTask $oportunity_task)
    {
        //
    }

    
    public function edit(OportunityTask $oportunity_task)
    {
        //
    }

    
    public function update(Request $request, OportunityTask $oportunity_task)
    {
        $validated = $request->validate([
            'asigned_id' => 'required',
            'limit_date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|string',
            'description' => 'required',
        ]);

        $oportunity_task->update($validated); 

        if ($request->comment) {
            $comment = new Comment([
                'body' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $oportunity_task->comments()->save($comment);
        }

        // event(new RecordEdited($task));

        return response()->json(['item' => OportunityTaskResource::make($oportunity_task->fresh(['asigned','oportunity','user','comments.user']))]);
    }

    
    public function destroy(OportunityTask $oportunity_task)
    {
        $oportunity_task->delete();
    }

    public function markAsDone($oportunity_task_id)
    {
        $oportunity_task = OportunityTask::find($oportunity_task_id);
        // return $oportunity_task;
        $oportunity_task->update([
            'finished_at' => now()
        ]);
    }

    public function comment(Request $request, OportunityTask $oportunity_task)
    {

        $comment = new Comment([
            'body' => $request->comment,
            'user_id' => auth()->id(),
        ]);

        $oportunity_task->comments()->save($comment);
        // event(new RecordCreated($comment)); me dice que el id del usuario no tiene un valor por default.
        // return to_route('projects.show', ['project' => $request->project_id]);
        return response()->json(['item' => $comment->fresh('user')]);
    }
}
