<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\CatalogProductResource;
use App\Models\CatalogProduct;
use App\Models\ProductionCost;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogProductController extends Controller
{

    public function index()
    {
        $catalog_products = CatalogProductResource::collection(CatalogProduct::latest()->get());
        return inertia('CatalogProduct/Index', compact('catalog_products'));
    }


    public function create()
    {
        $raw_materials = RawMaterial::all();
        $production_costs = ProductionCost::all();

        // consecutive
        $last = CatalogProduct::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);

        return inertia('CatalogProduct/Create', compact('raw_materials', 'production_costs', 'consecutive'));
    }


    public function store(Request $request)
    {
        // total production cost
        $total_cost = 0;

        $validated = $request->validate([
            'name' => 'required',
            'part_number' => 'required|string|unique:catalog_products,part_number',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable',
            'raw_materials.*.production_cost' => 'array|min:1'
        ]);

        // consecutive
        $last = CatalogProduct::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
        $validated['part_number'] .= $consecutive;
       
        $catalog_product = CatalogProduct::create($validated);
        $catalog_product->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            $costs = $product['production_costs'] ?? [];
            foreach ($costs as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordCreated($catalog_product));

        return to_route('catalog-products.index');
    }


    public function show(CatalogProduct $catalog_product)
    {
        $catalog_products = CatalogProductResource::collection(CatalogProduct::with('storages')->get());
        // return $catalog_products;
        return inertia('CatalogProduct/Show', compact('catalog_products', 'catalog_product'));
    }


    public function edit(CatalogProduct $catalog_product)
    {
        $catalog_product = CatalogProduct::with('rawMaterials')->find($catalog_product->id);
        $production_costs = ProductionCost::all();
        $raw_materials = RawMaterial::all();
        $media = $catalog_product->getFirstMedia();

        return inertia('CatalogProduct/Edit', compact('catalog_product', 'production_costs', 'raw_materials', 'media'));
    }


    public function update(Request $request, CatalogProduct $catalog_product)
    {
        $total_cost = 0;
        $request->validate([
            'name' => 'required',
            'part_number' => 'required|string',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable'
        ]);

        $catalog_product->update($request->all());
        $catalog_product->rawMaterials()->detach();

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            $costs = $product['production_costs'] ?? [];
            foreach ($costs as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordEdited($catalog_product));

        return to_route('catalog-products.index');
    }

    public function updateWithMedia(Request $request, CatalogProduct $catalog_product)
    {
        $total_cost = 0;
        $request->validate([
            'name' => 'required',
            'part_number' => 'required|string',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable'
        ]);

        $catalog_product->update($request->all());
        $catalog_product->rawMaterials()->detach();

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            foreach ($product['production_costs'] as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        // update image
        $catalog_product->clearMediaCollection();
        $catalog_product->addMediaFromRequest('media')->toMediaCollection();
        $catalog_product->save();

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordEdited($catalog_product));

        return to_route('catalog-products.index');
    }


    public function destroy(CatalogProduct $catalog_product)
    {
        $catalog_product_name = $catalog_product->name;
        $catalog_product->delete();

        event(new RecordDeleted($catalog_product));

        return response()->json(['message' => "Producto eliminado: $catalog_product_name"]);
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->catalog_products as $catalog_product) {
            $catalog_product = CatalogProduct::find($catalog_product['id']);
            $catalog_product?->delete();
            
            event(new RecordDeleted($catalog_product));
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function clone(Request $request)
    {
        $catalog_product = CatalogProduct::find($request->catalog_product_id);

        $clone = $catalog_product->replicate()->fill([
            'authorized_user_name' => null,
            'authorized_at' => null,
            'name' => $catalog_product->name . ' (Copia)',
            'part_number' => $catalog_product->part_number . '-Copia',
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();

        foreach ($catalog_product->rawMaterials as $product) {
            $pivot = [
                'quantity' => $product->pivot->quantity,
                'production_costs' => $product->pivot->production_costs,
            ];

            $clone->rawMaterials()->attach($product->pivot->raw_material_id, $pivot);
        }

        // cloning files
        $original_files = $catalog_product->getMedia();

        foreach ($original_files as $file) {
            $copy_file = $file->copy($clone); // Copy file to cloned item
        }

        $clone->save();

        return response()->json(['message' => "Producto clonado: {$clone->part_number}", 'newItem' => catalogProductResource::make(CatalogProduct::with('storages')->find($clone->id))]);
    }

    public function QRSearchCatalogProduct(Request $request)
    {

        $request->validate([ 
            'barCode' => 'required|string'
        ]);

        $part_number = explode('#', $request->barCode)[0];

        $catalog_product = CatalogProductResource::make(CatalogProduct::with('storages')->where('part_number', $part_number)->first());

        // return $catalog_product;


        return response()->json(['item' => $catalog_product]);
    }
}
