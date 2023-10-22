<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Http\Resources\MeetingMonitorResource;
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

   
    public function show($metting_monitor_id)
    {
        $metting_monitor = MeetingMonitorResource::make(MettingMonitor::with('seller', 'oportunity', 'company', 'companyBranch')->find($metting_monitor_id));

        // return $metting_monitor;
        return inertia('MettingMonitor/Show', compact('metting_monitor'));
    }

    
    public function edit(MettingMonitor $metting_monitor)
    {
        //
    }

    
    public function update(Request $request, MettingMonitor $metting_monitor)
    {
        //
    }

    
    public function destroy(MettingMonitor $metting_monitor)
    {
        //
    }
}
