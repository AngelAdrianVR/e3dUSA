<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogProductController extends Controller
{
    
    public function index()
    {
        $catalog_products = CatalogProduct::all();
        return inertia('CatalogProduct/Index', compact('catalog_products'));
    }

    
    public function create()
    {
        return inertia('CatalogProduct/Create');
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
        //
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
}
