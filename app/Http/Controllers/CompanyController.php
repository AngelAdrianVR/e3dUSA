<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\CatalogProductCompanyResource;
use App\Http\Resources\CompanyResource;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompany;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\CompanyProduct;
use App\Models\Contact;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = CompanyResource::collection(Company::with('companyBranches')->latest()->get());

        // return $companies;
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
            'products' => 'array|min:0',
        ]);

        $company = Company::create($request->except(['company_branches', 'products']));
        foreach ($request->company_branches as $branch) {
            $branch['company_id'] = $company->id;
            $compay_branch = CompanyBranch::create($branch);
            foreach ($branch['contacts'] as $contact) {
                $compay_branch->contacts()->create($contact);
            }

            foreach ($request->products as $product) {
                $company->catalogProducts()->attach($product['catalog_product_id'], $product);
            }
        }

        event(new RecordCreated($company));

        return to_route('companies.index');
    }


    public function show($company_id)
    {
        $company = Company::with('companyBranches.contacts')->find($company_id);
        $companies = CompanyResource::collection(Company::with('companyBranches.contacts', 'companyBranches.sales', 'companyBranches.quotes', 'catalogProducts.media', 'oportunities', 'clientMonitors.seller', 'projects.tasks')->get());

        // return $companies;

        return inertia('Company/Show', compact('company', 'companies'));
    }


    public function edit(Company $company)
    {
        $company = Company::with('catalogProducts', 'companyBranches.contacts')->find($company->id);
        $catalog_products = CatalogProduct::all();
        $raw_materials = RawMaterial::all();

        return inertia('Company/Edit', compact('company', 'catalog_products', 'raw_materials'));
    }


    public function update(Request $request, Company $company)
    {
        $request->validate([
            'business_name' => ['required', 'string', Rule::unique('companies')->ignore($company->id)],
            'phone' => 'required|min:10|max:12',
            'rfc' => ['required', 'string', Rule::unique('companies')->ignore($company->id)],
            'post_code' => 'required',
            'fiscal_address' => 'required',
            'company_branches' => 'array|min:1',
            'products' => 'array|min:0',
        ]);

        $company->update($request->except(['company_branches', 'products']));

        // Actualizar sucursales y contactos existentes
        foreach ($request->company_branches as $branchData) {
            if (isset($branchData['id'])) {
                $branch = CompanyBranch::findOrFail($branchData['id']);
                $branch->update($branchData);
                foreach ($branchData['contacts'] as $contactData) {
                    if (isset($contactData['id'])) {
                        $contact = Contact::findOrFail($contactData['id']);
                        $contact->update($contactData);
                    } else {
                        $branch->contacts()->create($contactData);
                    }
                }
            }
        }

        // Crear nuevas sucursales y contactos
        foreach ($request->company_branches as $branchData) {
            if (!isset($branchData['id'])) {
                $branch = $company->companyBranches()->create($branchData);

                foreach ($branchData['contacts'] as $contactData) {
                    $branch->contacts()->create($contactData);
                }
            }
        }

        // Actualizar productos
        $company->catalogProducts()->sync($request->products);

        event(new RecordEdited($company));

        return to_route('companies.index');
    }


    public function destroy(Company $company)
    {
        $company_name = $company->business_name;
        $company->delete();

        event(new RecordDeleted($company));

        return response()->json(['message' => "Producto eliminado: $company_name"]);
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->companies as $company) {
            $company = Company::find($company['id']);
            $company?->delete();

            event(new RecordDeleted($company));
        }

        return response()->json(['message' => 'Cliente(s) eliminado(s)']);
    }

    public function clone(Request $request)
    {
        $company = Company::find($request->company_id);

        $clone = $company->replicate()->fill([
            'business_name' => $company->business_name . ' (Copia)',
        ]);

        $clone->save();

        foreach ($company->companyBranches as $branch) {
            $branch = CompanyBranch::find($branch->id);
            $branch_clone = $branch->replicate()->fill([
                'company_id' => $clone->id
            ]);

            $branch_clone->save();

            foreach ($branch->contacts as $contact) {
                $branch_clone->contacts()->create($contact->toArray());
            }
        }

        foreach ($company->catalogProducts as $product) {
            $pivot = [
                'old_date' => $product->pivot->old_date,
                'new_date' => $product->pivot->new_date,
                'old_price' => $product->pivot->old_price,
                'new_price' => $product->pivot->new_price,
                'old_currency' => $product->pivot->old_currency,
                'new_currency' => $product->pivot->new_currency,
            ];

            $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
        }

        return response()->json(['message' => "Cliente clonado: {$clone->business_name}", 'newItem' => CompanyResource::make($clone)]);
    }

    public function getAllCompanies()
    {
        $companies = Company::with(['companyBranches.contacts'])->get();

        return response()->json(['items' => $companies]);
    }
}
