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
            $raw_materials = Storage::where('type', 'materia-prima')->get();
            return inertia('Storage/Index/RawMaterial', compact('raw_materials'));
        }elseif(Route::currentRouteName() == 'storages.consumables.index'){
            return inertia('Storage/Index/Consumable');
        }elseif(Route::currentRouteName() == 'storages.finished-products.index'){
            return inertia('Storage/Index/FinishedProduct');
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
}
