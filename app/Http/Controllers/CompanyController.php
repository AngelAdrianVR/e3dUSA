<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\CompanyResource;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompany;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Contact;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = CompanyResource::collection(Company::with(['companyBranches', 'seller:id,name'])
            ->latest()
            ->get(['id','business_name','phone','rfc','post_code', 'fiscal_address','seller_id']));

        return inertia('Company/Index', compact('companies'));
    }

    public function create()
    {
        $catalog_products = CatalogProduct::with(['media'])->get(['id', 'name']);
        $sellers = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereIn('id', [2, 3])
                    ->orWhere(function ($query) {
                        $query->whereIn('employee_properties->department', ['Ventas', 'Administración', 'Dirección']);
                    });
            })
            ->get(['id', 'name', 'profile_photo_path']);
        
        return inertia('Company/Create', compact('catalog_products', 'sellers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|unique:companies,business_name',
            'phone' => 'required|min:10|max:12',
            'rfc' => 'required|string|unique:companies,rfc',
            'post_code' => 'required',
            'fiscal_address' => 'required',
            'branches_number' => 'nullable|numeric|min:1',
            'seller_id' => 'required|numeric|min:0',
            'company_branches' => 'array|min:1',
            'products' => 'array|min:0',
            'suggested_products' => 'nullable|array|min:0',
        ]);

        $company = Company::create($request->except(['company_branches', 'products'] + ['user_id' => auth()->id()]));
        foreach ($request->company_branches as $branch) {
            // valor por defecto a dias para reactivar cliente para evitar error de base de datos si lo dejan nulo
            $branch['days_to_reactivate'] = $branch['days_to_reactivate'] ?? 30; 
            $branch['company_id'] = $company->id;
            $compay_branch = CompanyBranch::create($branch);
            foreach ($branch['contacts'] as $contact) {
                $compay_branch->contacts()->create($contact);
            }

            foreach ($request->products as $product) {
                $product['new_updated_by'] = auth()->user()->name;
                $product['old_updated_by'] = $product['old_price'] ? auth()->user()->name : null;
                $company->catalogProducts()->attach($product['catalog_product_id'], $product);
            }
        }

        event(new RecordCreated($company));

        return to_route('companies.show', $company);
    }

    public function show($company_id)
    {
        $company = CompanyResource::make(Company::with('user', 'seller', 'companyBranches.contacts', 'companyBranches.designAuthorizations', 'companyBranches.sales', 'companyBranches.sales.user', 'companyBranches.quotes', 'catalogProducts.media', 'oportunities', 'clientMonitors.seller', 'clientMonitors.emailMonitor', 'clientMonitors.paymentMonitor', 'clientMonitors.mettingMonitor', 'clientMonitors.whatsappMonitor', 'projects.tasks')->find($company_id));
        // $company = CompanyResource::make(Company::with(['user', 'seller', 'companyBranches.contacts', 'companyBranches.sales', 'companyBranches.sales.user', 'companyBranches.quotes', 'catalogProducts.media', 'oportunities', 'clientMonitors.seller', 'clientMonitors.emailMonitor', 'clientMonitors.paymentMonitor', 'clientMonitors.mettingMonitor', 'clientMonitors.whatsappMonitor', 'projects.tasks'])->find($company_id));
        $pre_companies = CompanyResource::make(Company::latest()->get());
        $companies = $pre_companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->business_name,
            ];
        });

        $defaultTab = request('defaultTab');

        return inertia('Company/Show', compact('company', 'companies', 'defaultTab'));
    }

    public function edit(Company $company)
    {
        $company = Company::with('catalogProducts', 'companyBranches.contacts')->find($company->id);
        $catalog_products = CatalogProduct::with(['media'])->get(['id', 'name']);
        $sellers = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereIn('id', [2, 3])
                    ->orWhere(function ($query) {
                        $query->whereIn('employee_properties->department', ['Ventas', 'Administración', 'Dirección']);
                    });
            })
            ->get(['id', 'name', 'profile_photo_path']);

        return inertia('Company/Edit', compact('company', 'catalog_products', 'sellers'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'business_name' => ['required', 'string', Rule::unique('companies')->ignore($company->id)],
            'phone' => 'required|min:10|max:12',
            'rfc' => ['required', 'string', Rule::unique('companies')->ignore($company->id)],
            'post_code' => 'required',
            'fiscal_address' => 'required',
            'branches_number' => 'nullable|numeric|min:1',
            'seller_id' => 'required|numeric|min:0',
            'company_branches' => 'array|min:1',
            'products' => 'array|min:0',
            'suggested_products' => 'nullable|array|min:0',
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
        // $company->catalogProducts()->sync($request->products);
        $productIdsInRequest = array_column($request->products, 'catalog_product_id');

        // Recorre los productos relacionados con la compañía
        foreach ($company->catalogProducts as $product) {
            $catalogProductId = $product->id;

            if (in_array($catalogProductId, $productIdsInRequest)) {
                // El producto existe en la solicitud, actualiza los datos
                $productData = $request->products[array_search($catalogProductId, $productIdsInRequest)];
                CatalogProductCompany::find($product->pivot->id)->update($productData);
                // $product->update($productData);
            } else {
                // El producto no está en la solicitud, elimínalo
                $company->catalogProducts()->detach($catalogProductId);
            }
        }

        // Relaciona los nuevos productos y actualiza cualquier otra información de la compañía
        foreach ($request->products as $productData) {
            $catalogProductId = $productData['catalog_product_id'];

            if (!$company->catalogProducts->contains($catalogProductId)) {
                $company->catalogProducts()->attach($catalogProductId, $productData);
            }
        }

        event(new RecordEdited($company));

        return to_route('companies.show', $company);
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

    public function getExclusiveDesigns(Company $company)
    {
        $items = $company->exclusiveDesigns->load(['media', 'user']);

        return response()->json(compact('items'));
    }

    public function contactsReport()
    {
        $company_branches = CompanyBranch::with('contacts:id,contactable_type,contactable_id,name,email,phone,birthdate_day,birthdate_month,charge')
            ->get(['id', 'name']);

        return inertia('Company/ContactsTemplate', compact('company_branches'));
    }

    // lo utilizo en el create de ov para agregar productos nuevos que cotizó el cliente desde el portal de clientes
    public function attachCatalogProduct(Request $request)
    {
        // Validación del request
        $validated = $request->validate([
            'new_price' => 'required|numeric|min:0',
            'new_currency' => 'required|string',
            'new_date' => 'required|date',
            'company_branch_id' => 'required|exists:company_branches,id',
        ]);

        // Obtener la sucursal y la empresa en una sola consulta
        $company_branch = CompanyBranch::with('company')->findOrFail($request->company_branch_id);
        $company = $company_branch->company;

        if (!$company) {
            return response()->json(['error' => 'La empresa no fue encontrada.'], 404);
        }

        // Datos para la relación
        $productData = [
            'new_price' => $validated['new_price'],
            'new_currency' => $validated['new_currency'],
            'new_date' => $validated['new_date'],
            'new_updated_by' => auth()->user()->name,
        ];

        // Agregar el producto a la empresa
        $company->catalogProducts()->attach($request->catalog_product_id, $productData);

        // Retirar el ID del producto de suggested_products si existe
        $suggestedProducts = $company->suggested_products ?? [];

        if (($key = array_search($request->catalog_product_id, $suggestedProducts)) !== false) {
            unset($suggestedProducts[$key]);
    
            // Guardar el array actualizado en la base de datos
            $company->update([
                'suggested_products' => array_values($suggestedProducts), // Reindexar el array
            ]);
        }
    }
}
