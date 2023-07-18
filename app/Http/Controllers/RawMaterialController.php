<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RawMaterialController extends Controller
{

    public function index()
    {
    }


    public function create()
    {
        if (Route::currentRouteName() == 'raw-materials.create') {

            return inertia('Storage/Create/RawMaterial');
        } elseif (Route::currentRouteName() == 'consumables.create') {

            return inertia('Storage/Create/Consumable');
        }
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
            'location' => 'required|string',
        ]);

        $raw_material = RawMaterial::create($request->all());
        $raw_material->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        $raw_material->storages()->create([
            'quantity' => $request->initial_stock,
            'type' => $request->type,
            'location' => $request->location,
        ]);


        if ($request->type == 'materia-prima')
            return to_route('storages.raw-materials.index');
        else
            return to_route('storages.consumables.index');
    }


    public function show(RawMaterial $rawMaterial)
    {
        //
    }


    public function edit(RawMaterial $raw_material)
    {

        return inertia('Storage/Edit/RawMaterial', compact('raw_material'));
    }

    public function editConsumable(RawMaterial $raw_material)
    {

        return inertia('Storage/Edit/Consumable', compact('raw_material'));
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
            'location' => 'required|string',
        ]);

        $raw_material->update($request->all());
        $raw_material->storages()->update([
            'quantity' => $request->initial_stock,
            'location' => $request->location,
        ]);

        if ($request->type == 'materia-prima')
            return to_route('storages.raw-materials.index');
        else
            return to_route('storages.consumables.index');
    }


    public function destroy(RawMaterial $raw_material)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->raw_materials as $raw_material) {
            $raw_material = Storage::find($raw_material['id']);
            $raw_material?->delete();
            $raw_material = RawMaterial::find($raw_material['storageable_id']);
            $raw_material?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }
}
