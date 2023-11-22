<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Http\Resources\ClientMonitorResource;
use App\Http\Resources\OportunityResource;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\PaymentMonitor;
use Illuminate\Http\Request;

class ClientMonitorController extends Controller
{
   
    public function index()
    {
        // $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('company', 'seller', 'oportunity', 'emailMonitor', 'paymentMonitor', 'mettingMonitor', 'whatsappMonitor')->latest()->get());
        $pre_client_monitors = ClientMonitorResource::collection(ClientMonitor::with('company', 'seller', 'oportunity', 'emailMonitor', 'paymentMonitor', 'mettingMonitor', 'whatsappMonitor')->latest()->get());
        $client_monitors = $pre_client_monitors->map(function ($client_monitor) {
            if ($client_monitor->oportunity) {
                $folio = 'S-' . strtoupper(substr($client_monitor->oportunity?->name, 0, 3)) . '-' . strtoupper(substr($client_monitor->type, 0, 1)) . str_pad($client_monitor->id, 4, '0', STR_PAD_LEFT);
            } else {
                $folio = 'S-' . strtoupper(substr($client_monitor->company?->business_name, 0, 3)) . '-' . strtoupper(substr($client_monitor->type, 0, 1)) . str_pad($client_monitor->id, 4, '0', STR_PAD_LEFT);
            }
            return [
                'id' => $client_monitor->id,    
                'folio' => $folio,
                'emailMonitorId' => $client_monitor->emailMonitor?->id,
                'paymentMonitorId' => $client_monitor->paymentMonitor?->id,
                'mettingMonitorId' => $client_monitor->mettingMonitor?->id,
                'whatsappMonitorId' => $client_monitor->whatsappMonitor?->id,
                'company_name' => $client_monitor->company?->business_name,
                'type' => $client_monitor->type,
                'concept' => $client_monitor->concept,
                'seller' => $client_monitor->seller->name,
                'date' => $client_monitor->date?->isoFormat('DD MMMM YYYY, h:mm A'),
                   ];
               });

        // return $client_monitors;

        return inertia('ClientMonitor/Index', compact('client_monitors'));
    }

    
    public function Create()
    {
        
    }

    public function meetingCreate()
    {
        
    }

    
    public function store(Request $request)
    {
        
    }

   
    public function show(ClientMonitor $client_monitor)
    {

        $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('oportunity')->latest()->get());

        // return $client_monitors;

        return inertia('ClientMonitor/Show', compact('client_monitor', 'client_monitors'));
    }

    
    public function edit(ClientMonitor $client_monitor)
    {
        //
    }

   
    public function update(Request $request, ClientMonitor $client_monitor)
    {
        //
    }

    
    public function destroy(ClientMonitor $client_monitor)
    {
        $client_monitor->delete();
        event(new RecordDeleted($client_monitor));
    }
}
