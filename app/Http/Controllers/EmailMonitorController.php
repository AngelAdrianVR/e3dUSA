<?php

namespace App\Http\Controllers;

use App\Http\Resources\OportunityResource;
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
        //
    }

    
    public function show(EmailMonitor $emailMonitor)
    {
        //
    }

    
    public function edit(EmailMonitor $emailMonitor)
    {
        //
    }

    
    public function update(Request $request, EmailMonitor $emailMonitor)
    {
        //
    }

    
    public function destroy(EmailMonitor $emailMonitor)
    {
        //
    }
}
