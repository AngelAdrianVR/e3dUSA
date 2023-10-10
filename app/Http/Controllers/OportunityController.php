<?php

namespace App\Http\Controllers;

use App\Http\Resources\OportunityResource;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class OportunityController extends Controller
{
    
    public function index()
    {
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks')->latest()->get());

        // return $oportunities;
        return inertia('Oportunity/Index',  compact('oportunities'));
    }

    
    public function create()
    {
        $users = User::where('is_active', true)->get();
        $companies = Company::with('companyBranches.contacts')->latest()->get();
        
        // return $companies;
        return inertia('Oportunity/Create', compact('users', 'companies'));
    }

    
    public function store(Request $request)
    {
       $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'description' => 'required',
            'tags' => 'nullable|array',
            'probability' => 'nullable|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'estimated_finish_date' => 'nullable|date',
            'type_access_project' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
        ]);

        Oportunity::create($validated + [ 'user_id' => auth()->id() ]);

        return to_route('oportunities.index');
    }

    
    public function show(Oportunity $oportunity)
    {
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks.asigned', 'oportunityTasks.oportunity', 'oportunityTasks.user', 'user', 'clientMonitores.seller')->latest()->get());
        $users = User::where('is_active', true)->get();

        // return $oportunities;
        return inertia('Oportunity/Show', compact('oportunity', 'oportunities', 'users'));
    }

    
    public function edit(Oportunity $oportunity)
    {
        //
    }

    
    public function update(Request $request, Oportunity $oportunity)
    {
        //
    }

    
    public function destroy(Oportunity $oportunity)
    {
        $oportunity->delete();
    }
}
