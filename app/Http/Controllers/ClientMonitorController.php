<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientMonitorResource;
use App\Models\ClientMonitor;
use Illuminate\Http\Request;

class ClientMonitorController extends Controller
{
   
    public function index()
    {
        $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('company', 'seller', 'oportunity')->latest()->get());

        // return $client_monitors;

        return inertia('ClientMonitor/Index', compact('client_monitors'));
    }

    
    public function paymentCreate()
    {
        $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('company')->latest()->get());

        // return $client_monitors;

        return inertia('ClientMonitor/PaymentCreate', compact('client_monitors'));
    }

    public function meetingCreate()
    {
        return inertia('ClientMonitor/MeetingCreate');
    }

    
    public function store(Request $request)
    {
        //
    }

   
    public function show(ClientMonitor $client_monitor)
    {
        //
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
    }
}
