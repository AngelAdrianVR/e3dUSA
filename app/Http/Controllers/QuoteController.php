<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuoteResource;
use App\Models\CatalogProduct;
use App\Models\CompanyBranch;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = QuoteResource::collection(Quote::latest()->get());

        return inertia('Quote/Index', compact('quotes'));
    }

    public function create()
    {
        $catalog_products = CatalogProduct::all();
        $company_branches = CompanyBranch::all();

        return inertia('Quote/Create', compact('catalog_products', 'company_branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|numeric|min:0',
            'freight_cost' => 'required|numeric|min:0',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:191',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'required|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $quote = Quote::create($request->except('products') + ['user_id' => auth()->id()]);

        foreach ($request->products as $product) {
            $quote->catalogProducts()->attach($product['id'], $product);
        }

        return to_route('quotes.index');
    }

    public function show(Quote $quote)
    {
        $quote = QuoteResource::make(Quote::findOrFail($quote->id));

        if ($quote->is_spanish_template)
            return inertia('Quote/SpanishTemplate', compact('quote'));
        else
            return inertia('Quote/EnglishTemplate', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $quote = Quote::with('catalogProducts')->find($quote->id);
        $catalog_products = CatalogProduct::all();
        $company_branches = CompanyBranch::all();

        return inertia('Quote/Edit', compact('catalog_products', 'company_branches', 'quote'));
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|numeric|min:0',
            'freight_cost' => 'required|numeric|min:0',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:191',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'required|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $quote->update($request->except('products'));
        $quote->catalogProducts()->detach();

        foreach ($request->products as $product) {
            $quote->catalogProducts()->attach($product['catalog_product_id'], $product);
        }

        return to_route('quotes.index');
    }

    public function destroy(Quote $quote)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->quotes as $quote) {
            $quote = Quote::find($quote['id']);
            $quote?->delete();
        }

        return response()->json(['message' => 'Cotización(es) eliminada(s)']);
    }

    public function clone(Request $request)
    {
        $quote = Quote::find($request->quote_id);

        $clone = $quote->replicate()->fill([
            'authorized_user_name' => null,
            'authorized_at' => null,
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();

        foreach ($quote->catalogProducts as $product) {
            $pivot = [
                'quantity' => $product->pivot->quantity,
                'price' => $product->pivot->price, 
                'notes' => $product->pivot->notes, 
                'show_image' => $product->pivot->show_image,
            ];

            $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
            $new_item_folio = 'COT-' . str_pad($clone->id, 3, "0", STR_PAD_LEFT);
        }

        return response()->json(['message' => "Cotización clonada: $new_item_folio", 'newItem' => QuoteResource::make($clone)]);
    }
}
