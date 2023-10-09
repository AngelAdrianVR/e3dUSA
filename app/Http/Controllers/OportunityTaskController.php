<?php

namespace App\Http\Controllers;

use App\Models\OportunityTask;
use Illuminate\Http\Request;

class OportunityTaskController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        return inertia('OportunityTask/Create');
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(OportunityTask $oportunityTask)
    {
        //
    }

    
    public function edit(OportunityTask $oportunityTask)
    {
        //
    }

    
    public function update(Request $request, OportunityTask $oportunityTask)
    {
        //
    }

    
    public function destroy(OportunityTask $oportunityTask)
    {
        //
    }
}
