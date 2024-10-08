<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\CatalogProduct;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index()
    {   
        $shipping_rates = CatalogProduct::has('shippingRates')->with('shippingRates:id,boxes,catalog_product_id')->paginate(20, ['id', 'name', 'part_number']);

        return inertia('ShippingRate/Index', compact('shipping_rates'));
    }

    public function create()
    {   
        $catalog_products = CatalogProduct::all(['id', 'name', 'part_number']);
        $box_types = Box::all();

        return inertia('ShippingRate/Create', compact('catalog_products', 'box_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalog_product_id' =>'required',
            'quantity' => 'required|numeric|min:1|max:999999',
            'boxes' => 'required|array|min:1',
            // Validar cada atributo dentro de cada objeto en boxes
            'boxes.*.box_id' => 'nullable|integer',
            'boxes.*.length' => 'required|numeric|min:0', // Largo de la caja
            'boxes.*.width' => 'required|numeric|min:0',  // Ancho de la caja
            'boxes.*.height' => 'required|numeric|min:0', // Alto de la caja
            'boxes.*.weight' => 'required|numeric|min:0', // Peso de la caja
            'boxes.*.quantity' => 'required|integer|min:1', // Cantidad de unidades en la caja
            'boxes.*.cost' => 'required|numeric|min:0', // Tarifa de la caja
        ]);

        if ($request->all_boxes_are_same) {
            $box = $request->boxes[0]; // Primer caja
            $request->merge(['boxes' => array_fill(0, $request->boxes_amount, $box)]); // Clona la primera caja
        }
        
        $shipping_rate = ShippingRate::create([
            'quantity' => $request->quantity,
            'boxes' => $request->boxes,
            'catalog_product_id' => $request->catalog_product_id,
        ]);

        return to_route('shipping-rates.show', $shipping_rate->id);
    }

    public function show(ShippingRate $shipping_rate)
    {   
        $shipping_rate->load([
            'catalogProduct.media',
            'catalogProduct.shippingRates'
        ]);
        $shipping_rates = CatalogProduct::has('shippingRates')->get(['id', 'name', 'part_number']);

        // return $shipping_rates;
        return inertia('ShippingRate/Show', compact('shipping_rate', 'shipping_rates'));
    }

    public function edit(ShippingRate $shipping_rate)
    {
        $catalog_products = CatalogProduct::all(['id', 'name', 'part_number']);
        $box_types = Box::all();

        // return $shipping_rate;
        return inertia('ShippingRate/Edit', compact('catalog_products', 'box_types', 'shipping_rate'));
    }

    public function update(Request $request, ShippingRate $shipping_rate)
    {
        $request->validate([
            'catalog_product_id' =>'required',
            'quantity' => 'required|numeric|min:1|max:999999',
            'boxes' => 'required|array|min:1',
            // Validar cada atributo dentro de cada objeto en boxes
            'boxes.*.box_id' => 'nullable|integer',
            'boxes.*.length' => 'required|numeric|min:0', // Largo de la caja
            'boxes.*.width' => 'required|numeric|min:0',  // Ancho de la caja
            'boxes.*.height' => 'required|numeric|min:0', // Alto de la caja
            'boxes.*.weight' => 'required|numeric|min:0', // Peso de la caja
            'boxes.*.quantity' => 'required|integer|min:1', // Cantidad de unidades en la caja
            'boxes.*.cost' => 'required|numeric|min:0', // Tarifa de la caja
        ]);

        $shipping_rate->update([
            'quantity' => $request->quantity,
            'boxes' => $request->boxes,
            'catalog_product_id' => $request->catalog_product_id,
        ]);

        return to_route('shipping-rates.show', $shipping_rate->id);
    }

    public function destroy(ShippingRate $shipping_rate)
    {
        $shipping_rate->delete();
    }

    public function fetchCatalogProductInfo(CatalogProduct $catalog_product)
    {
        $catalog_product->load('media');

        return response()->json(['item' => $catalog_product]);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');
        
        $shipping_rates = CatalogProduct::has('shippingRates')
            ->where(function($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                    ->orWhere('part_number', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->with('shippingRates:id,boxes,catalog_product_id')
            ->latest()
            ->paginate(100, ['id', 'name', 'part_number']);

        return response()->json(['items' => $shipping_rates], 200);
    }
}
