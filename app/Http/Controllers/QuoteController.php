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
        $quotes = QuoteResource::collection(Quote::all());

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

        foreach($request->products as $product) {
            $quote->catalogProducts()->attach($product['id'], $product);
        }

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
        //
    }

    public function update(Request $request, Quote $quote)
    {
        //
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
}
