<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Http\Resources\CallMonitorResource;
use App\Http\Resources\OportunityResource;
use App\Models\CallMonitor;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\User;
use Illuminate\Http\Request;

class CallMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        // $companies = Company::with('companyBranches.contacts')->get(['id', 'business_name']);

        $companies = Company::with(['companyBranches.contacts'])
        ->get(['id', 'business_name'])
        ->map(function ($company) {
            $company['company_branches'] = $company['companyBranches']->map(function ($branch) {
                $branch['contacts'] = $branch['contacts']->map(function ($contact) {
                    return [
                        'id' => $contact['id'],
                        'name' => $contact['name'],
                        'phone' => $contact['phone'],
                    ];
                });
                return [
                    'id' => $branch['id'],
                    'name' => $branch['name'],
                    'contacts' => $branch['contacts'],
                ];
            });
            unset($company['companyBranches']); // Eliminar la relación original después de la transformación
            return $company;
        });
   
        // $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());
        $oportunities = Oportunity::with('company')->latest()->get()
        ->map(function ($oportunity) {
            return [
                'id' => $oportunity->id,
                'folio' => 'OP-' . strtoupper(substr($oportunity->name, 0, 3)) . '-' . str_pad($oportunity->id, 3, '0', STR_PAD_LEFT),
                'name' => $oportunity->name,
                'company' => $oportunity->company,
            ];
        });

        $users = User::where('is_active', true)->whereNot('id', 1)->where('employee_properties->department', 'Ventas')->get();

        $opportunity = null;
        if (request('opportunityId')) {
            $opportunity = Oportunity::with(['companyBranch'])->find(request('opportunityId'));
        } else {
            $opportunity = null;
        }
        
        // return $users;
        return inertia('CallMonitor/Create', compact('companies', 'oportunities', 'users', 'opportunity'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'company_id' => 'nullable',
            'company_name' => 'nullable',
            'seller_id' => 'required',
            'contact_phone' => 'required',
            'date' => 'required|date',
            'notes' => 'nullable',
            'company_id' => 'nullable',
            'contact_id' => 'nullable',
            'company_branch_id' => 'nullable',
        ]);

        $call_monitor = CallMonitor::create($request->all());

        $client_monitor = ClientMonitor::create([
            'type' => 'Llamada',
            'date' => $request->date,
            'concept' => $request->notes,
            'seller_id' => $request->seller_id,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
            'monitor_id' => $call_monitor->id,
        ]);

        $call_monitor->client_monitor_id = $client_monitor->id;
        $call_monitor->save();

        event(new RecordCreated($call_monitor));

        
        return to_route('client-monitors.index');
    }

    
    public function show($call_monitor_id)
    {
        $call_monitor = CallMonitorResource::make(CallMonitor::with('seller', 'oportunity', 'contact', 'companyBranch')->find($call_monitor_id));

        // return $call_monitor;
        
        return inertia('CallMonitor/Show', compact('call_monitor'));
    }

    
    public function edit($call_monitor_id)
    {
        $call_monitor = CallMonitorResource::make(CallMonitor::with('oportunity', 'company', 'companyBranch', 'contact', 'seller')->find($call_monitor_id));
        
        // $companies = Company::with('companyBranches.contacts')->get();
        $companies = Company::with(['companyBranches.contacts'])
        ->get(['id', 'business_name'])
        ->map(function ($company) {
            $company['company_branches'] = $company['companyBranches']->map(function ($branch) {
                $branch['contacts'] = $branch['contacts']->map(function ($contact) {
                    return [
                        'id' => $contact['id'],
                        'name' => $contact['name'],
                        'phone' => $contact['phone'],
                    ];
                });
                return [
                    'id' => $branch['id'],
                    'name' => $branch['name'],
                    'contacts' => $branch['contacts'],
                ];
            });
            unset($company['companyBranches']); // Eliminar la relación original después de la transformación
            return $company;
        });

        // $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());
        $oportunities = Oportunity::with('company')->latest()->get()
        ->map(function ($oportunity) {
            return [
                'id' => $oportunity->id,
                'folio' => 'OP-' . strtoupper(substr($oportunity->name, 0, 3)) . '-' . str_pad($oportunity->id, 3, '0', STR_PAD_LEFT),
                'name' => $oportunity->name,
                'company' => $oportunity->company,
            ];
        });

        $users = User::where('is_active', true)->whereNot('id', 1)->where('employee_properties->department', 'Ventas')->get();

        // return $call_monitor;

        return inertia('CallMonitor/Edit', compact('call_monitor', 'companies', 'oportunities', 'users'));
    }

    
    public function update(Request $request, CallMonitor $call_monitor)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'company_id' => 'nullable',
            'company_name' => 'nullable',
            'seller_id' => 'required',
            'contact_phone' => 'required',
            'date' => 'required|date',
            'notes' => 'nullable',
            'company_id' => 'nullable',
            'contact_id' => 'nullable',
            'company_branch_id' => 'nullable',
        ]);

        $call_monitor->update($request->all());

        //recupero el seguimiento integral para actualizarlo tambien
        $client_monitor = ClientMonitor::where('oportunity_id', $call_monitor->oportunity_id)->first();

        $client_monitor->update([
            'date' => $request->date,
            'concept' => $request->notes,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        $call_monitor->client_monitor_id = $client_monitor->id;
        $call_monitor->save();

        event(new RecordCreated($call_monitor));

        
        return to_route('call-monitors.show', ['call_monitor'=> $call_monitor]);
    }

    
    public function destroy($call_monitor_id)
    {
        $call_monitor = CallMonitor::find($call_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $call_monitor_id)->where('type', 'Llamada')->first();
        $client_monitor->delete();
        $call_monitor->delete();
        event(new RecordDeleted($call_monitor));

        return to_route('client-monitors.index');
    }
}
