<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\CatalogProduct;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function index()
    {
        $companies = CompanyResource::collection(Company::latest()->get());

        return inertia('Company/Index', compact('companies'));
    }

    
    public function create()
    {
        $catalog_products = CatalogProduct::all();
        $raw_materials = RawMaterial::all();

        return inertia('Company/Create', compact('catalog_products', 'raw_materials'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|unique:companies,business_name',
            'phone' => 'required|min:10|max:12',
            'rfc' => 'required|string|unique:companies,rfc',
            'post_code' => 'required',
            'fiscal_address' => 'required',
            'company_branches' => 'array|min:1',
            'products' => 'nullable|array|min:1',
        ]);

        $company = Company::create($request->except(['company_branches', 'products']));
         foreach ($request->company_branches as $branch) {
            $branch['company_id'] = $company->id;
            CompanyBranch::create($branch);
         }

        return to_route('companies.index');
    }

    
    public function show(Company $company)
    {
        return inertia('Company/Show');
    }

    
    public function edit(Company $company)
    {
        return inertia('Company/Edit', compact('company'));
    }

    
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'business_name' => 'required|string|unique:companies,business_name',
            'phone' => 'required|min:10|max:13',
            'rfc' => 'required|string|unique:companies,rfc',
            'post_code' => 'required',
            'fiscal_address' => 'required',
        ]);

        $company->update($request->all());

        return to_route('companies.index');
    }

    
    public function destroy(Company $company)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->companies as $company) {
            $company = Company::find($company['id']);
            $company?->delete();
        }

        return response()->json(['message' => 'Cliente(s) eliminado(s)']);
    }

    public function clone(Request $request)
    {
        $company = CatalogProduct::find($request->company_id);

        // $clone = $company->replicate()->fill([
        //     'authorized_user_name' => null,
        //     'authorized_at' => null,
        //     'name' => $company->name . ' (Copia)',
        //     'part_number' => $company->part_number . '-Copia',
        //     'user_id' => auth()->id(),
        //     'sale_id' => null,
        // ]);

        // $clone->save();

        // foreach ($company->rawMaterials as $product) {
        //     $pivot = [
        //         'quantity' => $product->pivot->quantity,
        //         'production_costs' => $product->pivot->production_costs, 
        //     ];

        //     $clone->rawMaterials()->attach($product->pivot->raw_material_id, $pivot);
        // }

        // return response()->json(['message' => "Producto clonado: {$clone->part_number}", 'newItem' => catalogProductResource::make($clone)]);
    }
}
