<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\MeetingMonitorResource;
use App\Http\Resources\OportunityResource;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\MettingMonitor;
use App\Models\Oportunity;
use App\Models\User;
use Illuminate\Http\Request;

class MettingMonitorController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        $companies = Company::with('companyBranches.contacts')->get();
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());
        $users = User::where('is_active', true)->whereNot('id', 1)->get();
        $opportunity = null;
        if (request('opportunityId')) {
            $opportunity = Oportunity::with(['companyBranch'])->find(request('opportunityId'));
        } else {
            $opportunity = null;
        }
        return inertia('MettingMonitor/Create', compact('companies', 'oportunities', 'users', 'opportunity'));
    }

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
            'participants' => 'required|array|min:1',
        ]);

        $meeting_monitor = MettingMonitor::create($request->all() + ['seller_id' => auth()->id()]);
        
        event(new RecordCreated($meeting_monitor));

       $client_monitor = ClientMonitor::create([
            'type' => 'Reunión',
            'date' => $request->meeting_date,
            'concept' => $request->description,
            'seller_id' => auth()->id(),
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
            'monitor_id' => $meeting_monitor->id,
        ]);

        $meeting_monitor->client_monitor_id = $client_monitor->id;
        $meeting_monitor->save();

        event(new RecordCreated($client_monitor));
        
        return to_route('client-monitors.index');
    }

   
    public function show($metting_monitor_id)
    {
        $metting_monitor = MeetingMonitorResource::make(MettingMonitor::with('seller', 'oportunity', 'company', 'companyBranch', 'contact')->find($metting_monitor_id));

        return inertia('MettingMonitor/Show', compact('metting_monitor'));
    }

    
    public function edit($metting_monitor_id)
    {
        $metting_monitor = MeetingMonitorResource::make(MettingMonitor::with('oportunity', 'company', 'companyBranch', 'contact')->find($metting_monitor_id));
        $companies = Company::with('companyBranches.contacts')->get();
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());
        $users = User::where('is_active', true)->whereNot('id', 1)->get();

        return inertia('MettingMonitor/Edit', compact('metting_monitor', 'oportunities', 'companies', 'users'));
    }

    
    public function update(Request $request, $metting_monitor_id)
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
            'participants' => 'required|array|min:1',
        ]);

        $metting_monitor = MettingMonitor::find($metting_monitor_id);
        
        $metting_monitor->update($request->all());
        
        event(new RecordEdited($metting_monitor));
        
        $client_monitor = ClientMonitor::where('oportunity_id', $metting_monitor->oportunity_id)->first();
        

        $client_monitor->update([
            'date' => $request->meeting_date,
            'concept' => $request->description,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);
        
        return to_route('meeting-monitors.show', ['meeting_monitor'=> $metting_monitor]);
    }

    
    public function destroy($metting_monitor_id)
    {
        $metting_monitor = MettingMonitor::find($metting_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $metting_monitor_id)->where('type', 'Reunión')->first();
        $client_monitor->delete();
        $metting_monitor->delete();
        event(new RecordDeleted($metting_monitor));

        return to_route('client-monitors.index');
    }
}
