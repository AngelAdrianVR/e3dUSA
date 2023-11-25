<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProductionCostResource;
use App\Models\ProductionCost;
use Carbon\Carbon;
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
            'time' => 'required|after:00:00:00',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        // Convierte el campo 'time' a un objeto Carbon y resta 6 horas
        $time = Carbon::parse($request->time)->subHours(6);

        $production_cost = ProductionCost::create($request->except('time') + ['time' => $time]);

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
            'name' => 'required|string|max:190',
            'time' => 'required|after:00:00:00',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        // Convierte el campo 'time' a un objeto Carbon y resta 6 horas
        $time = Carbon::parse($request->time)->subHours(6);

        $production_cost->update($request->except('time') + ['time' => $time]);

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
