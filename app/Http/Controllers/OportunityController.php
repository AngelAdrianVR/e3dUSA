<?php

namespace App\Http\Controllers;

use App\Http\Resources\OportunityResource;
use App\Models\Oportunity;
use Illuminate\Http\Request;

class OportunityController extends Controller
{
    
    public function index()
    {
        $oportunities = OportunityResource::collection(Oportunity::latest()->get());

        // return $oportunities;
        return inertia('Oportunity/Index',  compact('oportunities'));
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
        $oportunity->delete();
    }
}
