<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
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
        $companies = Company::with('companyBranches.contacts')->get();
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());
        $users = User::where('is_active', true)->get();

        // return $oportunities;

        return inertia('WhatsappMonitor/Create', compact('companies', 'oportunities', 'users'));
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
        ]);

        $whatsapp_monitor->client_monitor_id = $client_monitor->id;
        $whatsapp_monitor->save();

        event(new RecordCreated($whatsapp_monitor));

        
        return to_route('client-monitors.index');
    }

    
    public function show($whatsapp_monitor_id)
    {
        $whatsapp_monitor = WhatsappMonitorResource::make(WhatsappMonitor::with('seller', 'oportunity', 'media')->find($whatsapp_monitor_id));

        // return $whatsapp_monitor;
        
        return inertia('WhatsappMonitor/Show', compact('whatsapp_monitor'));
    }

    
    public function edit(WhatsappMonitor $whatsapp_monitor)
    {
        //
    }

    
    public function update(Request $request, WhatsappMonitor $whatsapp_monitor)
    {
        //
    }

    
    public function destroy($whatsapp_monitor_id)
    {
        $whatsapp_monitor = WhatsappMonitor::find($whatsapp_monitor_id);
        $client_monitor = ClientMonitor::where('oportunity_id', $whatsapp_monitor->oportunity_id)->first();
        $client_monitor->delete();
        $whatsapp_monitor->delete();
        event(new RecordDeleted($whatsapp_monitor));

        return to_route('client-monitors.index');
    }
}
