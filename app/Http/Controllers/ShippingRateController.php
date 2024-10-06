<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index()
    {   
        $shipping_rates = ShippingRate::all();

        return inertia('ShippingRate/Index', compact('shipping_rates'));
    }

    public function create()
    {   
        $catalog_products = CatalogProduct::all(['id', 'name', 'part_number']);

        return inertia('ShippingRate/Create', compact('catalog_products'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ShippingRate $shippingRate)
    {
        //
    }

    public function edit(ShippingRate $shippingRate)
    {
        //
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        //
    }

    public function destroy(ShippingRate $shippingRate)
    {
        //
    }

    public function fetchCatalogProductInfo(CatalogProduct $catalog_product)
    {
        $catalog_product->load('media');

        return response()->json(['item' => $catalog_product]);
    }
}
