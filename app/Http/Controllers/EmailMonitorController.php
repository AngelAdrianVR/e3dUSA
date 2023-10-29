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
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailMonitor $emailMonitor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailMonitor $emailMonitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailMonitor $emailMonitor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailMonitor $emailMonitor)
    {
        //
    }
}
