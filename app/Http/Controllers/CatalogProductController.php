<?php

namespace App\Http\Controllers;

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

        return inertia('CatalogProduct/Create', compact('raw_materials', 'production_costs'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'part_number' => 'required|string|unique:catalog_products,part_number',
            'measure_unit' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'required'
        ]);

        CatalogProduct::create($request->all());

        return to_route('catalog-products.index');
    }

    
    public function show(CatalogProduct $catalogProduct)
    {
        return inertia('CatalogProduct/Show');
    }

    
    public function edit(CatalogProduct $catalog_product)
    {
        return inertia('CatalogProduct/Edit', compact('catalog_product'));
    }

    
    public function update(Request $request, CatalogProduct $catalog_product)
    {
        $request->validate([
            'name' => 'required',
            'part_number' => 'required|string',
            'measure_unit' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'required'
        ]);

        $catalog_product->update($request->all());

        return to_route('catalog-products.index');
    }

    
    public function destroy(CatalogProduct $catalogProduct)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->catalog_products as $catalog_product) {
            $catalog_product = CatalogProduct::find($catalog_product['id']);
            $catalog_product?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function clone(Request $request)
    {
        $catalog_product = CatalogProduct::find($request->catalog_product_id);

        $clone = $catalog_product->replicate()->fill([
            'authorized_user_name' => null,
            'authorized_at' => null,
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();

        // foreach ($catalog_product->catalogProducts as $product) {
        //     $pivot = [
        //         'quantity' => $product->pivot->quantity,
        //         'price' => $product->pivot->price, 
        //         'notes' => $product->pivot->notes, 
        //         'show_image' => $product->pivot->show_image,
        //     ];

        //     $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
        //     $new_item_folio = 'COT-' . str_pad($clone->id, 3, "0", STR_PAD_LEFT);
        // }

        // return response()->json(['message' => "Cotización clonada: $new_item_folio", 'newItem' => catalog_productResource::make($clone)]);
    }
}
