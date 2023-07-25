<?php

namespace App\Http\Controllers;

use App\Http\Resources\StorageResource;
use App\Models\CatalogProduct;
use App\Models\RawMaterial;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StorageController extends Controller
{
    
    public function index()
    {
        if(Route::currentRouteName() == 'storages.raw-materials.index'){
            $raw_materials = Storage::with('storageable')->where('type', 'materia-prima')->latest()->get();
            return inertia('Storage/Index/RawMaterial', compact('raw_materials'));

        }elseif(Route::currentRouteName() == 'storages.consumables.index'){
            $raw_materials = Storage::with('storageable')->where('type', 'consumible')->latest()->get();
            return inertia('Storage/Index/Consumable', compact('raw_materials'));

        }elseif(Route::currentRouteName() == 'storages.finished-products.index'){
            $finished_products = Storage::with('storageable.media')->where('type', 'producto-terminado')->latest()->get();

            // Calcular la suma de costo de todo el scrap
            $totalFinishedProductMoney = collect($finished_products)->sum(function ($item) {
            return $item->storageable->cost * $item->quantity;
            });

            return inertia('Storage/Index/FinishedProduct',compact('finished_products', 'totalFinishedProductMoney'));
        }else {

            $scraps = Storage::with('storageable.media')->where('type', 'scrap')->latest()->get();

            // Calcular la suma de costo de todo el scrap
            $totalScrapMoney = collect($scraps)->sum(function ($item) {
                return $item->storageable->cost * $item->quantity;
            });

            return inertia('Storage/Index/Scrap', compact('scraps', 'totalScrapMoney'));
        }
    }

    
    public function create()
    {

        if(Route::currentRouteName() == 'storages.scraps.create'){
            $storages = Storage::with('storageable.media')->get();
            return inertia('Storage/Create/Scrap', compact('storages'));
        }elseif(Route::currentRouteName() == 'storages.finished-products.create'){
            $catalog_products = CatalogProduct::with('media')->get();
            return inertia('Storage/Create/FinishedProduct', compact('catalog_products'));
        }
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'storageable_id' => 'required',
            'quantity' => 'required|numeric|min:1',
            'type' => 'required|string',
            'location' => 'required|string',

        ]);

        $finished_products = CatalogProduct::find($request->storageable_id);
        $finished_products->storages()->create([
            'quantity' => $request->quantity,
            'location' => $request->location,
            'type' => $request->type,
        ]);
        
        return to_route('storages.finished-products.index');
    }

    public function scrapStore(Request $request)
    {
        $storage = Storage::find($request->storage_id);
        $request->storage_id ? $stock = $storage?->quantity : $stock = 1;
        $request->validate([
            'storage_id' => 'required',
            'quantity' => 'required|numeric|min:1|max:' . $stock,
            'location' => 'required|string',
            'type' => 'required',
            
        ]);
        
        if($storage->type == 'materia-prima' || $storage->type == 'consumible' ){
            
            $raw_material = RawMaterial::find($storage->storageable_id);
            $raw_material->storages()->create([
                'quantity' => $request->quantity,
                'location' => $request->location,
                'type' => $request->type,
            ]);
            
        }else{
            
            $finished_products = CatalogProduct::find($storage->storageable_id);
            $finished_products->storages()->create([
                'quantity' => $request->quantity,
                'location' => $request->location,
                'type' => $request->type,
            ]);
            
        }

        $storage->quantity -= $request->quantity;
        $storage->save();
        
        return to_route('storages.scraps.index');
    }

    
    public function show($storage_id)
    {
        $storage = Storage::with('storageable.media')->find($storage_id);
        $storages = Storage::with('storageable.media')->where('type', '!=', 'scrap')->get();
       
        // Calcular la suma de la variable "cost" de todos los objetos
    $totalStorageMoney = collect($storages)->sum(function ($item) {
        return $item->storageable->cost * $item->quantity;
    });
        // return $totalStorageMoney;

        return inertia('Storage/Show', compact('storage', 'storages', 'totalStorageMoney'));
    }

    
    public function edit(Storage $storage)
    {
        $catalog_products = CatalogProduct::all();
        return inertia('Storage/Edit/FinishedProduct', compact('catalog_products', 'storage'));
    }

    
    public function update(Request $request, Storage $storage)
    {
        //
    }

    
    public function destroy(Storage $storage)
    {
        $storage->storageable->delete();
        $storage->delete();
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->finished_products as $finished_product) {
            $finished_product = Storage::find($finished_product['id']);
            $finished_product?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function scrapMassiveDelete(Request $request)
    {
        foreach ($request->scraps as $scrap) {
            $scrap = Storage::find($scrap['id']);
            if($scrap->storageable_type == 'App\Models\RawMaterial'){
                $scrap_restored = Storage::where('storageable_id', $scrap->storageable_id)
                ->where('type', '!=', 'scrap')
                ->where('type', '!=', 'producto-terminado')
                ->first();
            }else{
                $scrap_restored = Storage::where('storageable_id', $scrap->storageable_id)
                ->where('type', '!=', 'scrap')
                ->where('type', '!=', 'consumible')
                ->where('type', '!=', 'materia-prima')
                ->first();
            }
            $scrap_restored->increment('quantity', $scrap->quantity);
            $scrap?->delete();
        }

        return response()->json(['message' => 'Producto(s) retirado(s) de scrap']);
    }

    public function addStorage(Request $request, Storage $storage)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        $storage->update([
            'quantity' => $storage->quantity += $request->quantity
        ]);

        return response()->json(['item' => $storage]);
    }

    public function subStorage(Request $request, Storage $storage)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1|max:' . $storage?->quantity
        ]);

        $storage->update([
            'quantity' => $storage->quantity -= $request->quantity
        ]);

        return response()->json(['item' => $storage]);
    }
}
