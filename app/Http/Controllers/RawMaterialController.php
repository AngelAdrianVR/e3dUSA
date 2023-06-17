<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    
    public function index()
    {

    }

    
    public function create()
    {
        return inertia('Storage/Create/RawMaterial');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'part_number' => 'required|string|unique:raw_materials,part_number',
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        RawMaterial::create($request->all());

        return to_route('storages.raw-materials.index');
    }

    
    public function show(RawMaterial $rawMaterial)
    {
        //
    }

    
    public function edit(RawMaterial $raw_material)
    {
        return inertia('Storage/Edit/RawMaterial', compact('raw_material'));
    }

    
    public function update(Request $request, RawMaterial $raw_material)
    {
        $request->validate([
            'name' => 'required|string',
            'part_number' => 'required|string',
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $raw_material->update($request->all());

        return to_route('storages.raw-materials.index');
    }

    
    public function destroy(RawMaterial $raw_material)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->raw_materials as $raw_material) {
            $raw_material = RawMaterial::find($raw_material['id']);
            $raw_material?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }
}
