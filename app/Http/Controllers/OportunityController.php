<?php

namespace App\Http\Controllers;

use App\Models\Oportunity;
use Illuminate\Http\Request;

class OportunityController extends Controller
{
    
    public function index()
    {
        return inertia('Oportunity/Index');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Oportunity $oportunity)
    {
        //
    }

    
    public function edit(Oportunity $oportunity)
    {
        //
    }

    
    public function update(Request $request, Oportunity $oportunity)
    {
        //
    }

    
    public function destroy(Oportunity $oportunity)
    {
        //
    }
}
