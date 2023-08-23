<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\QuoteResource;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompany;
use App\Models\CatalogProductCompanySale;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Quote;
use App\Models\Sale;
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
        $can_authorize = auth()->user()->can('Autorizar cotizaciones') || auth()->user()->hasRole('Super admin');

        if($can_authorize) {
            $quote->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        }

        foreach ($request->products as $product) {
            $quote->catalogProducts()->attach($product['id'], $product);
        }

        event(new RecordCreated($quote));

        return to_route('quotes.index');
    }

    public function show(Quote $quote)
    {
        $quote = QuoteResource::make(Quote::with('catalogProducts')->findOrFail($quote->id));

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

        event(new RecordEdited($quote));

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
            $new_item_folio = 'COT-' . str_pad($clone->id, 4, "0", STR_PAD_LEFT);
        }

        return response()->json(['message' => "Cotización clonada: $new_item_folio", 'newItem' => QuoteResource::make($clone)]);
    }

    public function createSO(Request $request)
    {
        $quote = Quote::find($request->quote_id);
        $folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
        $branch = CompanyBranch::find($quote->company_branch_id);

        $sale = Sale::create([
            'freight_cost' => $quote->freight_cost,
            'order_via' => "Cotización folio $folio",
            'authorized_user_name' => auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin') ? auth()->user()->name : null,
            'authorized_at' => auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin') ? now() : null,
            'user_id' => auth()->id(),
            'company_branch_id' => $quote->company_branch_id,
        ]);

        $sale_folio = 'OV-' . str_pad($sale->id, 4, "0", STR_PAD_LEFT);

        // add products for sale to sale
        foreach ($quote->catalogProducts as $product) {
            $catalog_product_company = $branch->company->catalogProducts->first(fn ($item) => $item->id == $product->id);
            if (!$catalog_product_company) {
                // register products to company if any required
                $pivot = [
                    'new_date' => today(),
                    'new_price' => $product->pivot->price,
                    'new_currency' => $quote->currency,
                ];
                $branch->company->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
                $branch = CompanyBranch::find($quote->company_branch_id);
                $catalog_product_company = $branch->company->catalogProducts->last();
            }

            CatalogProductCompanySale::create([
                'catalog_product_company_id' => $catalog_product_company->pivot->id,
                'sale_id' => $sale->id,
                'quantity' => $product->pivot->quantity,
                'notes' => $product->pivot->notes,
            ]);
        }

        return response()->json(['message' => "Cotización convertida en orden de venta con folio: {$sale_folio}"]);
    }

   public function authorizeQuote(Quote $quote)
   {
        $quote->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        return response()->json(['message' => 'Cotizacion autorizadda', 'item' => $quote]);
   }
}
