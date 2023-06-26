<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
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
        $company_branches = CompanyBranch::with('company.catalogProducts')->get();

        return inertia('Sale/Create', compact('company_branches'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'shopping_company' => 'nullable',
            'freight_cost' => 'required',
            'order_via' => 'required',
            'tracking_guide' => 'nullable',
            'notes' => 'nullable',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $sale = Sale::create($request->except('products') + ['user_id' => auth()->id()]);

        foreach ($request->products as $product) {
            $sale->catalogProductsCompany()->attach($product['catalog_product_company_id'], $product);
        }

        return to_route('sales.index');
    }


    public function show(Sale $sale)
    {
        //
    }


    public function edit(Sale $sale)
    {
        //
    }


    public function update(Request $request, Sale $sale)
    {
        //
    }


    public function destroy(Sale $sale)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->sales as $sale) {
            $sale = Sale::find($sale['id']);
            $sale?->delete();
        }

        return response()->json(['message' => 'OV(s) eliminada(s)']);
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

        foreach ($sale->catalogProductsCompany as $product) {
            $pivot = [
                'quantity' => $product->pivot->quantity,
                'notes' => $product->pivot->notes,
                'status' => null,
                'assigned_jobs' => null,
            ];

            $clone->catalogProductsCompany()->attach($product->pivot->catalog_product_company_id, $pivot);
        }

        return response()->json(['message' => "OV clonada: {$clone->id}", 'newItem' => saleResource::make($clone)]);
    }
}
