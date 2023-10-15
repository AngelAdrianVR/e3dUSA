<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityResource;
use App\Models\ClientMonitor;
use App\Models\Oportunity;
use App\Models\PaymentMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PaymentMonitorController extends Controller
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
        $oportunities = OportunityResource::collection(Oportunity::with('company')->latest()->get());

        // return $oportunities;

        return inertia('ClientMonitor/PaymentCreate', compact('oportunities'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        $payment_monitor->addAllMediaFromRequest('media')->each(fn ($file) => $file->toMediaCollection('files'));

        ClientMonitor::create([
            'type' => 'Pago',
            'date' => now(),
            'concept' => $request->concept,
            'seller_id' => $request->seller_id,
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        event(new RecordCreated($payment_monitor));
        
        return to_route('client-monitors.index');
    }

    
    public function show(PaymentMonitor $payment_monitor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMonitor $payment_monitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMonitor $payment_monitor)
    {
        // event(new RecordEdited($payment_monitor));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMonitor $payment_monitor)
    {
        // event(new RecordDeleted($payment_monitor));
    }
}
