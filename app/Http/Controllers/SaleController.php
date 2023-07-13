<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\CatalogProductCompanySale;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Sale;
use Illuminate\Http\Request;


class SaleController extends Controller 
{

    public function index()
    {
        $sales = SaleResource::collection(Sale::with('companyBranch', 'user')->latest()->get());

        return inertia('Sale/Index', compact('sales'));
    }


    public function create()
    {
        $company_branches = CompanyBranch::with('company.catalogProducts', 'contacts')->get();

        return inertia('Sale/Create', compact('company_branches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'shipping_company' => 'nullable',
            'freight_cost' => 'required|numeric|min:0',
            'order_via' => 'required',
            'tracking_guide' => 'nullable',
            'notes' => 'nullable',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $sale = Sale::create($request->except('products') + ['user_id' => auth()->id()]);
        
        // store media
        $sale->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('oce'));

        foreach ($request->products as $product) {
            CatalogProductCompanySale::create($product + ['sale_id' => $sale->id]);
        }

        return to_route('sales.index');
    }


    public function show(Sale $sale)
    {
        $sales = SaleResource::collection(Sale::with('user', 'companyBranch', 'contact')->latest()->get());
        // return $sales;       
        return inertia('Sale/Show', compact('sale', 'sales'));
    }


    public function edit(Sale $sale)
    {
        $sale = Sale::find($sale->id);
        $catalog_products_company_sale = CatalogProductCompanySale::where('sale_id', $sale->id)->get();

        $company_branches = CompanyBranch::with('company.catalogProducts', 'contacts')->get();

        return inertia('Sale/Edit', compact('company_branches', 'sale', 'catalog_products_company_sale'));
    }


    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'shipping_company' => 'nullable',
            'freight_cost' => 'required|numeric|min:0',
            'order_via' => 'required',
            'tracking_guide' => 'nullable',
            'invoice' => 'nullable',
            'notes' => 'nullable',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $sale->update($request->except('products'));
        $sale->catalogProductsCompany()->detach();

        foreach ($request->products as $product) {
            CatalogProductCompanySale::create($product + ['sale_id' => $sale->id]);
        }

        return to_route('sales.index');
    }


    public function destroy(Sale $sale)
    {
        $sale->delete();
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->sales as $sale) {
            $sale = Sale::find($sale['id']);
            $sale?->delete();
        }

        return response()->json(['message' => 'OV(s) eliminada(s)']);
    }

    public function authorize(Sale $sale)
    {
        $sale->update([
            'authorized_at' => now()
        ]);
    }

    public function clone(Request $request)
    {
        $sale = Sale::find($request->sale_id);

        $clone = $sale->replicate()->fill([
            'status' => 0,
            'oce_name' => null,
            'tracking_guide' => null,
            'authorized_user_name' => null,
            'authorized_at' => null,
            'recieved_at' => null,
            'user_id' => auth()->id(),
        ]);

        $clone->save();

        $catalog_products_company_sale = CatalogProductCompanySale::where('sale_id', $request->sale_id)->get();
        foreach ($catalog_products_company_sale as $product) {
            $pivot = [
                'catalog_product_company_id' => $product->catalog_product_company_id,
                'quantity' => $product->quantity,
                'notes' => $product->notes,
                'status' => null,
                'assigned_jobs' => null,
            ];

            CatalogProductCompanySale::create($pivot + ['sale_id' => $clone->id]);
        }

        $new_item_folio = 'OV-' . str_pad($clone->id, 4, "0", STR_PAD_LEFT);

        return response()
            ->json([
                'message' => "Orden de venta clonada: $new_item_folio", 'newItem' => saleResource::make(Sale::with('companyBranch', 'user')->find($clone->id))
            ]);
    }

    
}
