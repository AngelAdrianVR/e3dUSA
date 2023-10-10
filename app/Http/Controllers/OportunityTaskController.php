<?php

namespace App\Http\Controllers;

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
        //
    }

    
    public function destroy(OportunityTask $oportunity_task)
    {
        //
    }

    public function markAsDone($oportunity_task_id)
    {
        $oportunity_task = OportunityTask::find($oportunity_task_id);
        // return $oportunity_task;
        $oportunity_task->update([
            'finished_at' => now()
        ]);
    }
}
