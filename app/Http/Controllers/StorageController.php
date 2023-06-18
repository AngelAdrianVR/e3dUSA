<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StorageController extends Controller
{
    
    public function index()
    {
        if(Route::currentRouteName() == 'storages.raw-materials.index'){
            $raw_materials = Storage::with('storageable')->where('type', 'materia-prima')->get();
            return inertia('Storage/Index/RawMaterial', compact('raw_materials'));

        }elseif(Route::currentRouteName() == 'storages.consumables.index'){
            $raw_materials = Storage::with('storageable')->where('type', 'consumible')->get();
            return inertia('Storage/Index/Consumable', compact('raw_materials'));

        }elseif(Route::currentRouteName() == 'storages.finished-products.index'){
            $finished_products = Storage::with('storageable')->where('type', 'producto-terminado')->get();
            return inertia('Storage/Index/FinishedProduct',compact('finished_products'));

        }else
        return inertia('Storage/Index/Scrap');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Storage $storage)
    {
        //
    }

    
    public function edit(Storage $storage)
    {
        //
    }

    
    public function update(Request $request, Storage $storage)
    {
        //
    }

    
    public function destroy(Storage $storage)
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
