<?php

namespace App\Http\Controllers;

use App\Http\Resources\RawMaterialResource;
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
        $last = RawMaterial::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;

        if (Route::currentRouteName() == 'raw-materials.create') {

            return inertia('Storage/Create/RawMaterial');
        } elseif (Route::currentRouteName() == 'consumables.create') {

            return inertia('Storage/Create/Consumable');
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([ 
            'name' => 'required|string',
            'part_number' => 'required|string|unique:raw_materials,part_number',
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'required|string',
            'location' => 'required|string',
        ]);

        // consecutive
        $last = RawMaterial::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
        $validated['part_number'] .= $consecutive;

        $raw_material = RawMaterial::create($validated);
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


    public function edit($raw_material)
    {
       $raw_material = RawMaterialResource::make(RawMaterial::with('storages')->find($raw_material));
        // return $raw_material;

        return inertia('Storage/Edit/RawMaterial', compact('raw_material'));
    }

    public function editConsumable($raw_material)
    {

        $raw_material = RawMaterialResource::make(RawMaterial::with('storages')->find($raw_material));
        // return $raw_material;
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

    public function updateWithMedia(Request $request, RawMaterial $raw_material)
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

          // update image
        $raw_material->clearMediaCollection();
        $raw_material->addAllMediaFromRequest('media')->each(fn ($file) => $file->toMediaCollection());

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
