<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionProgress;
use Illuminate\Http\Request;

class ProductionProgressController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
            'progress' => 'required|string',
            'notes' => 'nullable|string',
            'production_id' => 'required|numeric|min:1',
        ]);

        ProductionProgress::create($request->all());
        $production = Production::find($request->production_id);
        $production->is_paused = 1;
        $production->save();
    }

    public function show(ProductionProgress $productionProgress)
    {
        //
    }

    public function edit(ProductionProgress $productionProgress)
    {
        //
    }

    public function update(Request $request, ProductionProgress $productionProgress)
    {
        //
    }

    public function destroy(ProductionProgress $productionProgress)
    {
        //
    }
}
