<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Http\Resources\OportunityResource;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\EmailMonitor;
use App\Models\Oportunity;
use App\Models\User;
use Illuminate\Http\Request;

class EmailMonitorController extends Controller
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

        return inertia('EmailMonitor/Create', compact('companies', 'oportunities', 'users'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'oportunity_id' => 'required',
            'company_id' => 'nullable',
            'company_branch_id' => 'nullable',
            'contact_id' => 'nullable',
            'contact_email' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $email_monitor = EmailMonitor::create($request->all() + ['seller_id' => auth()->id()]);
        
        event(new RecordCreated($email_monitor));

       $client_monitor = ClientMonitor::create([
            'type' => 'Correo electrónico',
            'date' => now(),
            'concept' => $request->subject,
            'seller_id' => auth()->id(),
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
        ]);

        $email_monitor->client_monitor_id = $client_monitor->id;
        $email_monitor->save();

        event(new RecordCreated($client_monitor));
        
        return to_route('client-monitors.index');
    }

    
    public function show(EmailMonitor $email_monitor)
    {
        return inertia('EmailMonitor/Show');
    }

    
    public function edit(EmailMonitor $email_monitor)
    {
        //
    }

    
    public function update(Request $request, EmailMonitor $email_monitor)
    {
        //
    }

    
    public function destroy(EmailMonitor $email_monitor)
    {
        //
    }
}
