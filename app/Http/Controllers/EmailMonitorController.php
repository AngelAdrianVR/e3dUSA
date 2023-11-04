<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Http\Resources\EmailMonitorResource;
use App\Http\Resources\OportunityResource;
use App\Mail\EmailMonitorTemplateMail;
use App\Models\ClientMonitor;
use App\Models\Company;
use App\Models\EmailMonitor;
use App\Models\Oportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $users = User::where('is_active', true)->whereNot('id', 1)->get();

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
            'type' => 'Correo',
            'date' => now(),
            'concept' => $request->subject,
            'seller_id' => auth()->id(),
            'oportunity_id' => $request->oportunity_id,
            'company_id' => $request->company_id,
            'monitor_id' => $email_monitor->id,
        ]);

        $email_monitor->client_monitor_id = $client_monitor->id;
        $email_monitor->save();

        event(new RecordCreated($client_monitor));

        // enviar correo a contacto
        if (app()->environment() == 'production') {
            Mail::to($request->contact_email)->send(new EmailMonitorTemplateMail($request->subject, $request->content));
        }

        return to_route('client-monitors.index');
    }

    
    public function show($email_monitor_id)
    {
        $email_monitor = EmailMonitorResource::make(EmailMonitor::with('seller', 'oportunity', 'company', 'companyBranch')->find($email_monitor_id));

        // return $email_monitor;

        return inertia('EmailMonitor/Show', compact('email_monitor'));
    }


    public function edit(EmailMonitor $email_monitor)
    {
        //
    }


    public function update(Request $request, EmailMonitor $email_monitor)
    {
        //
    }

    
    public function destroy($email_monitor_id)
    {
        $email_monitor = EmailMonitor::find($email_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $email_monitor->id)->where('type', 'Correo')->first();
        $client_monitor->delete();
        $email_monitor->delete();
        event(new RecordDeleted($email_monitor));

        return to_route('client-monitors.index');
    }
}
