<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProductionCostResource;
use App\Models\ProductionCost;
use Illuminate\Http\Request;

class ProductionCostController extends Controller
{
    
    public function index()
    {
        $production_costs = ProductionCostResource::collection(ProductionCost::latest()->get());

        return inertia('ProductionCost/Index', compact('production_costs'));
    }

    
    public function create()
    {
        return inertia('ProductionCost/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $production_cost = ProductionCost::create($request->all());

        event(new RecordCreated($production_cost));
        
        return to_route('production-costs.index');
    }

    
    public function show(ProductionCost $production_cost)
    {
        //
    }

    
    public function edit(ProductionCost $production_cost)
    {
        return inertia('ProductionCost/Edit', compact('production_cost'));
    }


    public function update(Request $request, ProductionCost $production_cost)
    {
        $request->validate([
            'name' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $production_cost->update($request->all());

        event(new RecordEdited($production_cost));
        
        return to_route('production-costs.index');
    }

    
    public function destroy(ProductionCost $production_cost)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->production_costs as $production_cost) {
            $production_cost = ProductionCost::find($production_cost['id']);
            $production_cost?->delete();

            event(new RecordDeleted($production_cost));
        }

        return response()->json(['message' => 'Costo(s) de producci+on eliminado(s)']);
    }
}
