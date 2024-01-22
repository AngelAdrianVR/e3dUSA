<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityResource;
use App\Http\Resources\WhatsappMonitorResource;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\User;
use App\Models\WhatsappMonitor;
use Illuminate\Http\Request;

class WhatsappMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
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
        $oportunities = Oportunity::with('company:id,business_name')->latest()->get()
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

        // return $oportunities;

        return inertia('WhatsappMonitor/Create', compact('companies', 'oportunities', 'users', 'opportunity'));
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

        $whatsapp_monitor = WhatsappMonitor::create($request->all());

        $whatsapp_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());


        $client_monitor = ClientMonitor::create([
            'type' => 'WhatsApp',
            'date' => $request->date,
            'concept' => $request->notes ?? 'Interacción vía WhatsApp',
            'seller_id' => $request->seller_id,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
            'monitor_id' => $whatsapp_monitor->id,
        ]);

        $whatsapp_monitor->client_monitor_id = $client_monitor->id;
        $whatsapp_monitor->save();

        event(new RecordCreated($whatsapp_monitor));

        
        return to_route('client-monitors.index');
    }

    
    public function show($whatsapp_monitor_id)
    {
        $whatsapp_monitor = WhatsappMonitorResource::make(WhatsappMonitor::with('seller', 'oportunity', 'media', 'contact', 'companyBranch')->find($whatsapp_monitor_id));

        // return $whatsapp_monitor;
        
        return inertia('WhatsappMonitor/Show', compact('whatsapp_monitor'));
    }

    
    public function edit($whatsapp_monitor_id)
    {
        $whatsapp_monitor = WhatsappMonitorResource::make(WhatsappMonitor::with('oportunity', 'company', 'companyBranch', 'contact', 'seller')->find($whatsapp_monitor_id));

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

        // return $whatsapp_monitor;

        return inertia('WhatsappMonitor/Edit', compact('whatsapp_monitor', 'companies', 'oportunities', 'users'));
    }

    
    public function update(Request $request, WhatsappMonitor $whatsapp_monitor)
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

        $whatsapp_monitor->update($request->all());

        //recupero el seguimiento integral para actualizarlo tambien
        $client_monitor = ClientMonitor::where('oportunity_id', $whatsapp_monitor->oportunity_id)->first();

        $client_monitor->update([
            'date' => $request->date,
            'concept' => $request->notes ?? 'Interacción vía WhatsApp',
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        $whatsapp_monitor->client_monitor_id = $client_monitor->id;
        $whatsapp_monitor->save();

        event(new RecordCreated($whatsapp_monitor));

        
        return to_route('whatsapp-monitors.show', ['whatsapp_monitor'=> $whatsapp_monitor]);
    }

    public function updateWithMedia(Request $request, WhatsappMonitor $whatsapp_monitor)
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

        $whatsapp_monitor->update($request->all());

        $whatsapp_monitor->clearMediaCollection();
        $whatsapp_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        event(new RecordCreated($whatsapp_monitor));

        //recupero el seguimiento integral para actualizarlo tambien
        $client_monitor = ClientMonitor::where('oportunity_id', $whatsapp_monitor->oportunity_id)->first();

        $client_monitor->update([
            'date' => $request->date,
            'concept' => $request->notes ?? 'Interacción vía WhatsApp',
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        $whatsapp_monitor->client_monitor_id = $client_monitor->id;
        $whatsapp_monitor->save();


        
        return to_route('whatsapp-monitors.show', ['whatsapp_monitor'=> $whatsapp_monitor]);
    }

    
    public function destroy($whatsapp_monitor_id)
    {
        $whatsapp_monitor = WhatsappMonitor::find($whatsapp_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $whatsapp_monitor_id)->where('type', 'WhatsApp')->first();
        $client_monitor->delete();
        $whatsapp_monitor->delete();
        event(new RecordDeleted($whatsapp_monitor));

        return to_route('client-monitors.index');
    }
}
