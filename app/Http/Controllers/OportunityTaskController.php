<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityTaskResource;
use App\Models\Comment;
use App\Models\OportunityTask;
use App\Models\User;
use App\Notifications\MentionNotification;
use Illuminate\Http\Request;

class OportunityTaskController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($oportunity_id)
    {
        $users = User::where('is_active', true)->whereNot('id', 1)->get();

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

        event(new RecordCreated($oportunity_task));

        // archivos adjuntos
        $oportunity_task->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('oportunities.show', ['oportunity'=> $oportunity_task->oportunity_id, 'defaultTab' => 2]);
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

        event(new RecordEdited($oportunity_task));

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
        $oportunity_task->comments()->delete();
        $oportunity_task->delete();
        event(new RecordDeleted($oportunity_task));
    }

    public function markAsDone($oportunity_task_id)
    {
        $oportunity_task = OportunityTask::find($oportunity_task_id);
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

        $mentions = $request->mentions;
        foreach ($mentions as $mention) {
            $user = User::find($mention['id']);
            $user->notify(new MentionNotification($oportunity_task, "", 'opportunities'));
        }
        
        event(new RecordCreated($comment));
        return response()->json(['item' => $comment->fresh('user')]);
    }
}
