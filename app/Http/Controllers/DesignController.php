<?php

namespace App\Http\Controllers;

use App\Http\Resources\DesignResource;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    
    public function index()
    {
        $designs = DesignResource::collection(Design::with('user', 'designer')->latest()->get());

        return inertia('Design/Index', compact('designs'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Design $design)
    {
        $designs = DesignResource::collection(Design::with('user', 'designer')->latest()->get());
        return inertia('Design/Show', compact('design', 'designs'));
    }

    
    public function edit(Design $design)
    {
        //
    }

    
    public function update(Request $request, Design $design)
    {
        //
    }

    
    public function destroy(Design $design)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->companies as $company) {
            $company = Design::find($company['id']);
            $company?->delete();
        }

        return response()->json(['message' => 'Cliente(s) eliminado(s)']);
    }
}
