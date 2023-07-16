<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductionResource;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    
    public function index()
    {
        $productions = ProductionResource::collection(Production::with('user', 'catalogProductCompanySale.catalogProductCompany.company')->latest()->get());
        // return $productions;
        return inertia('Production/Index',compact('productions'));
    }

    
    public function create()
    {
        $production_users = User::where('employee_properties->department', 'Producción')->get();
        // return $production_users;
        return inertia('Production/Create', compact('production_users'));
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Production $production)
    {
        //
    }

    
    public function edit(Production $production)
    {
        //
    }

    
    public function update(Request $request, Production $production)
    {
        //
    }

    
    public function destroy(Production $production)
    {
        //
    }


    public function massiveDelete(Request $request)
    {
        foreach ($request->productions as $production) {
            $production = Production::find($production['id']);
            $production?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }
}
