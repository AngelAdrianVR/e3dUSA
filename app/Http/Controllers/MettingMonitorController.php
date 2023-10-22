<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Http\Resources\OportunityResource;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\MettingMonitor;
use App\Models\Oportunity;
use Illuminate\Http\Request;

class MettingMonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::with('companyBranches.contacts')->get();
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());

        return inertia('MettingMonitor/Create', compact('companies', 'oportunities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'is_oportunity' => 'boolean',
            'meeting_date' => 'required|date',
            'time' => 'required',
            'oportunity_id' => 'nullable',
            'company_id' => 'nullable',
            'company_branch_id' => 'nullable',
            'contact_id' => 'nullable',
            'contact_name' => 'nullable|string',
            'phone' => 'nullable',
            'meeting_via' => 'required',
            'location' => 'nullable',
            'description' => 'required',
        ]);

        $meeting_monitor = MettingMonitor::create($request->all() + ['seller_id' => auth()->id()]);
        
        event(new RecordCreated($meeting_monitor));

       $client_monitor = ClientMonitor::create([
            'type' => 'Reunión',
            'date' => now(),
            'concept' => $request->description,
            'seller_id' => auth()->id(),
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        event(new RecordCreated($client_monitor));
        
        return to_route('client-monitors.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(MettingMonitor $mettingMonitor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MettingMonitor $mettingMonitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MettingMonitor $mettingMonitor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MettingMonitor $mettingMonitor)
    {
        //
    }
}
