<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Http\Resources\StorageResource;
use App\Models\CatalogProduct;
use App\Models\RawMaterial;
use App\Models\StockMovementHistory;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StorageController extends Controller
{

    public function index()
    {
        if (Route::currentRouteName() == 'storages.raw-materials.index') {
            $raw_materials = StorageResource::collection(Storage::with('storageable')->where('type', 'materia-prima')->latest()->get());
            // return $raw_materials;
            // Calcular la suma de costo de todo el materia prima
            $totalRawMaterialMoney = collect($raw_materials)->sum(function ($item) {
                return $item->storageable?->cost * $item->quantity;
            });

            return inertia('Storage/Index/RawMaterial', compact('raw_materials', 'totalRawMaterialMoney'));
        } elseif (Route::currentRouteName() == 'storages.consumables.index') {
            $raw_materials = Storage::with('storageable')->where('type', 'consumible')->latest()->get();

            // Calcular la suma de costo de todo el materia prima
            $totalConsumableMoney = collect($raw_materials)->sum(function ($item) {
                return $item->storageable?->cost * $item->quantity;
            });
            return inertia('Storage/Index/Consumable', compact('raw_materials', 'totalConsumableMoney'));
        } elseif (Route::currentRouteName() == 'storages.finished-products.index') {
            $finished_products = Storage::with('storageable.media')->where('type', 'producto-terminado')->latest()->get();

            // Calcular la suma de costo de todo el scrap
            $totalFinishedProductMoney = collect($finished_products)->sum(function ($item) {
                return $item->storageable?->cost * $item->quantity;
            });

            return inertia('Storage/Index/FinishedProduct', compact('finished_products', 'totalFinishedProductMoney'));
        } else {

            $scraps = Storage::with('storageable.media')->where('type', 'scrap')->latest()->get();

            // Calcular la suma de costo de todo el scrap
            $totalScrapMoney = collect($scraps)->sum(function ($item) {
                return $item->storageable?->cost * $item->quantity;
            });

            return inertia('Storage/Index/Scrap', compact('scraps', 'totalScrapMoney'));
        }
    }


    public function create()
    {

        if (Route::currentRouteName() == 'storages.scraps.create') {
            $storages = Storage::with('storageable.media')->get();
            return inertia('Storage/Create/Scrap', compact('storages'));
        } elseif (Route::currentRouteName() == 'storages.finished-products.create') {
            $catalog_products = CatalogProduct::with('media')->latest()->get();
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

        event(new RecordCreated($finished_products));

        return to_route('storages.finished-products.index');
    }

    public function scrapStore(Request $request)
    {
        $storage = Storage::find($request->storage_id);
        $stock = $request->storage_id ? $storage?->quantity : 2;
        $request->validate([
            'storage_id' => 'required',
            'quantity' => 'required|numeric|min:1|max:' . $stock,
            'location' => 'required|string',
            'type' => 'required',

        ]);

        if ($storage->type == 'materia-prima' || $storage->type == 'consumible') {

            $raw_material = RawMaterial::find($storage->storageable_id);
            $raw_material->storages()->create([
                'quantity' => $request->quantity,
                'location' => $request->location,
                'type' => $request->type,
            ]);
            event(new RecordCreated($raw_material));
        } else {

            $finished_products = CatalogProduct::find($storage->storageable_id);
            $finished_products->storages()->create([
                'quantity' => $request->quantity,
                'location' => $request->location,
                'type' => $request->type,
            ]);
            event(new RecordCreated($finished_products));
        }

        $storage->quantity -= $request->quantity;
        $storage->save();

        

        return to_route('storages.scraps.index');
    }


    public function show($storage_id)
    {
        $storage = Storage::with('storageable.media', 'movements.user')->find($storage_id);
        $storages = Storage::with('storageable.media', 'movements.user')->where('type', 'materia-prima')->get();

        // Calcular la suma de la variable "cost" de todos los objetos
        $totalStorageMoney = collect($storages)->sum(function ($item) {
            return $item->storageable?->cost * $item->quantity;
        });
        // return $storage;

        return inertia('Storage/Show', compact('storage', 'storages', 'totalStorageMoney'));
    }

    public function showConsumable($storage_id)
    {
        $storage = Storage::with('storageable.media', 'movements.user')->find($storage_id);
        $storages = Storage::with('storageable.media', 'movements.user')->where('type', 'consumible')->get();


        return inertia('Storage/ShowConsumable', compact('storage', 'storages'));
    }


    public function edit(Storage $storage)
    {
        $catalog_products = CatalogProduct::with('media')->latest()->get();
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

        event(new RecordDeleted($storage));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->finished_products as $finished_product) {
            $finished_product = Storage::find($finished_product['id']);
            $finished_product?->delete();

            event(new RecordDeleted($finished_product));
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function scrapMassiveDelete(Request $request)
    {
        foreach ($request->scraps as $scrap) {
            $scrap = Storage::find($scrap['id']);
            if ($scrap->storageable_type == 'App\Models\RawMaterial') {
                $scrap_restored = Storage::where('storageable_id', $scrap->storageable_id)
                    ->where('type', '!=', 'scrap')
                    ->where('type', '!=', 'producto-terminado')
                    ->first();
            } else {
                $scrap_restored = Storage::where('storageable_id', $scrap->storageable_id)
                    ->where('type', '!=', 'scrap')
                    ->where('type', '!=', 'consumible')
                    ->where('type', '!=', 'materia-prima')
                    ->first();
            }
            $scrap_restored->increment('quantity', $scrap->quantity);
            $scrap?->delete();

            event(new RecordDeleted($scrap));
        }

        return response()->json(['message' => 'Producto(s) retirado(s) de scrap']);
    }

    public function addStorage(Request $request, Storage $storage)
    {

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable',
            'type' => 'required|string'
        ]);

        // Add movement to history
        $history = StockMovementHistory::Create($validated + ['storage_id' => $storage->id, 'user_id' => auth()->id()]);
        $history = StockMovementHistory::with('user')->find($history->id);

        // update stock
        $storage->update([
            'quantity' => $storage->quantity += $request->quantity
        ]);

        return response()->json(['item' => $storage, 'movement' => $history]);
    }

    public function subStorage(Request $request, Storage $storage)
    {
        
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1|max:' . $storage->quantity,
            'notes' => 'nullable',
            'type' => 'required|string'
        ]);

        // Add movement to history
        $history = StockMovementHistory::Create($validated + ['storage_id' => $storage->id, 'user_id' => auth()->id()]);
        $history = StockMovementHistory::with('user')->find($history->id);

        // update stock
        $storage->update([
            'quantity' => $storage->quantity -= $request->quantity
        ]);

        return response()->json(['item' => $storage, 'movement' => $history]);
    }

    public function QRStorage(Request $request)
    {
        $request->validate([
            'barCode' => 'required|string'
        ]);

        
        $part_number = explode('#', $request->barCode)[0];
        $quantity = explode('#', $request->barCode)[1];
        
        // Verifica si el formato es incorrecto (contiene "'") y realiza el reemplazo solo en ese caso
    if (strpos($part_number, "'") !== false) {
        $part_number = str_replace("'", "-", $part_number);
    }

    if (strpos($part_number, "´") !== false) {
        $part_number = str_replace("´", "-", $part_number);
    }

        $storage = Storage::whereHas('storageable', function ($query) use ($part_number) {
            $query->where('part_number', $part_number);
        })->first();

        if (!$storage) {
            return response()->json(['message' => 'No se encontró ningun producto con el número de parte ' . $part_number]);
        }

        if ($request->scanType == 'Entrada') {
            $storage->increment('quantity', $quantity);
        }else{
            if ($quantity > $storage->quantity ) {
                return response()->json([
                    'message' => 'Solo hay ' . $storage->quantity . ' ' . $storage->storageable->measure_unit . ' en almacén'
                ], 422);
            }else{

                $storage->decrement('quantity', $quantity);
            }
        }

        $history = StockMovementHistory::Create([
            'storage_id' => $storage->id, 'user_id' => auth()->id(),
            'quantity' => $quantity,
            'type' => $request->scanType,
            'notes' => 'Movimiento generado mediante QR.',
        ]);


        return response()->json(['message' => "Se ha generado un movimiento para el producto {$storage->storageable->name}"]);
    }

    public function QRSearchProduct(Request $request)
    {

        $request->validate([ 
            'barCode' => 'required|string'
        ]);

        $part_number = explode('#', $request->barCode)[0];

        // Verifica si el formato es incorrecto (contiene "'") y realiza el reemplazo solo en ese caso
            if (strpos($part_number, "'") !== false) {
                $part_number = str_replace("'", "-", $part_number);
            }

            if (strpos($part_number, "´") !== false) {
                $part_number = str_replace("´", "-", $part_number);
            }

        $storage = Storage::with('storageable.media')->whereHas('storageable', function ($query) use ($part_number) {
            $query->where('part_number', $part_number);
        })->first();


        return response()->json(['item' => $storage]);
    }
}
