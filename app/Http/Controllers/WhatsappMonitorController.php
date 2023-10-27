<?php

namespace App\Http\Controllers;

use App\Http\Resources\OportunityResource;
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

        return inertia('WhatsappMonitor/Create', compact('companies', 'oportunities', 'users'));
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(WhatsappMonitor $whatsappMonitor)
    {
        //
    }

    
    public function edit(WhatsappMonitor $whatsappMonitor)
    {
        //
    }

    
    public function update(Request $request, WhatsappMonitor $whatsappMonitor)
    {
        //
    }

    
    public function destroy(WhatsappMonitor $whatsappMonitor)
    {
        //
    }
}
