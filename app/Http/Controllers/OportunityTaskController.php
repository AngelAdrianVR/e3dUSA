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

    
    public function create()
    {
        $users = User::where('is_active', true)->get();

        return inertia('OportunityTask/Create', compact('users'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'asigned_id' => 'required',
            'limit_date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|string',
            'description' => 'required',
        ]);

        $oportunity_task = OportunityTask::create($request->except('media') + ['user_id' => auth()->id()]);

        return to_route('oportunities.show', ['oportunity'=> $oportunity_task->oportunity_id]);
    }

    
    public function show(OportunityTask $oportunityTask)
    {
        //
    }

    
    public function edit(OportunityTask $oportunityTask)
    {
        //
    }

    
    public function update(Request $request, OportunityTask $oportunityTask)
    {
        //
    }

    
    public function destroy(OportunityTask $oportunityTask)
    {
        //
    }
}
