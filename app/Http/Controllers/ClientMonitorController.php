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
        $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('company', 'seller', 'oportunity')->latest()->get());

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
