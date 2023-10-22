<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityResource;
use App\Http\Resources\PaymentMonitorResource;
use App\Models\ClientMonitor;
use App\Models\Oportunity;
use App\Models\PaymentMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PaymentMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());

        // return $oportunities;

        return inertia('PaymentMonitor/Create', compact('oportunities'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'seller_id' => 'required',
            'paid_at' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'concept' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor = PaymentMonitor::create($request->all());

        $payment_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());


        $client_monitor = ClientMonitor::create([
            'type' => 'Pago',
            'date' => now(),
            'concept' => $request->concept,
            'seller_id' => $request->seller_id,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        $payment_monitor->client_monitor_id = $client_monitor->id;
        $payment_monitor->save();

        event(new RecordCreated($payment_monitor));
        
        return to_route('client-monitors.index');
    }

    
    public function show($payment_monitor_id)
    {
        $payment_monitor = PaymentMonitorResource::make(PaymentMonitor::with('seller', 'oportunity', 'media')->find($payment_monitor_id));

        return inertia('PaymentMonitor/Show', compact('payment_monitor'));
    }

    
    public function edit($payment_monitor_id)
    {
        $payment_monitor = PaymentMonitorResource::make(PaymentMonitor::with('oportunity')->find($payment_monitor_id));
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());

        // return $payment_monitor;

        return inertia('PaymentMonitor/Edit', compact('payment_monitor', 'oportunities'));
    }

    
    public function update(Request $request, PaymentMonitor $payment_monitor)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'paid_at' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'concept' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor->update($request->all());

        event(new RecordEdited($payment_monitor));
        
        return to_route('client-monitors.index');
    }

    public function updateWithMedia(Request $request, PaymentMonitor $payment_monitor)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'paid_at' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'concept' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor->update($request->all());

        $payment_monitor->clearMediaCollection();
        $payment_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        // event(new RecordEdited($payment_monitor));
        
        return to_route('payment-monitors.show', ['payment_monitor'=> $payment_monitor]);
    }

    
    public function destroy($payment_monitor_id)
    {   
        $payment_monitor = PaymentMonitor::find($payment_monitor_id);
        $client_monitor = ClientMonitor::where('oportunity_id', $payment_monitor->oportunity_id)->first();
        $client_monitor->delete();
        $payment_monitor->delete();
        event(new RecordDeleted($payment_monitor));

        return to_route('client-monitors.index');
    }
}
