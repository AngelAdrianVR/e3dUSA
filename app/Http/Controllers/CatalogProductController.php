<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use Illuminate\Http\Request;

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

    
    public function edit(CatalogProduct $catalogProduct)
    {
        //
    }

    
    public function update(Request $request, CatalogProduct $catalogProduct)
    {
        //
    }

    
    public function destroy(CatalogProduct $catalogProduct)
    {
        //
    }
}
