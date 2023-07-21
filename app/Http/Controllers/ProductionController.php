<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductionResource;
use App\Http\Resources\SaleResource;
use App\Models\Production;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    public function index()
    {
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de produccion todas')) {
            // $productions = ProductionResource::collection(Production::with('user', 'catalogProductCompanySale.catalogProductCompany.company')->latest()->get());
            $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale', 'companyBranch')->whereHas('productions')->latest()->get());
            // return $productions;
            return inertia('Production/Admin', compact('productions'));
        } elseif (auth()->user()->can('Ordenes de produccion personal')) {
            $productions = ProductionResource::collection(Production::with('user', 'catalogProductCompanySale.catalogProductCompany.company')->where('user_id', auth()->id())->latest()->get());
            return inertia('Production/Index', compact('productions'));
        } else {
            $productions = ProductionResource::collection(Production::with('user', 'catalogProductCompanySale.catalogProductCompany.company')->where('operator_id', auth()->id())->latest()->get());
            return inertia('Production/Operator', compact('productions'));
        }
    }

    public function create()
    {
        $operators = User::where('employee_properties->department', 'Producción')->get();
        $sales = SaleResource::collection(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct')->whereNotNull('authorized_at')->whereDoesntHave('productions')->get());

        // return $sales;
        return inertia('Production/Create', compact('operators', 'sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productions' => 'array|min:1',
        ]);

        foreach ($request->productions as $production) {
            $foreigns = [
                'user_id' => $production['user_id'],
                'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
            ];

            foreach ($production['tasks'] as $task) {
                $data = $task + $foreigns;

                Production::create($data);
            }
        }

        return to_route('productions.index');
    }


    public function show($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['contact', 'companyBranch.company', 'catalogProductCompanySales.catalogProductCompany.catalogProduct.media', 'productions' => ['user', 'operator']])->find($sale_id));
        $sales = SaleResource::collection(Sale::with(['contact', 'companyBranch.company', 'catalogProductCompanySales.catalogProductCompany.catalogProduct.media', 'productions' => ['user', 'operator']])->whereHas('productions')->get());

        // return compact('sale', 'sales');
        return inertia('Production/Show', compact('sale', 'sales'));
    }

    public function edit($sale_id)
    {
        $operators = User::where('employee_properties->department', 'Producción')->get();
        $sale = SaleResource::make(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct', 'productions')->find($sale_id));

        // return $sale;
        return inertia('Production/Edit', compact('operators', 'sale'));
    }

    public function update(Request $request, $sale_id)
    {
        $request->validate([
            'productions' => 'array|min:1',
        ]);

        $sale = Sale::find($sale_id);
        $sale->productions()->delete();

        foreach ($request->productions as $production) {
            $foreigns = [
                'user_id' => $production['user_id'],
                'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
            ];

            foreach ($production['tasks'] as $task) {
                $data = $task + $foreigns;

                Production::create($data);
            }
        }

        return to_route('productions.index');
    }

    public function destroy(Production $production)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->productions as $production) {
            $production = Production::find($production['id']);
            $production?->delete();
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }
}
