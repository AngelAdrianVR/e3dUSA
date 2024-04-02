<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\SaleResource;
use App\Models\CatalogProductCompanySale;
use App\Models\CompanyBranch;
use App\Models\DesignAuthorization;
use App\Models\Oportunity;
use App\Models\Sale;
use App\Models\Sample;
use App\Models\StockMovementHistory;
use App\Models\Storage;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use Illuminate\Http\Request;
use Nette\Utils\Strings;

class SaleController extends Controller
{

    public function index()
    {
        $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name'])->where('user_id', auth()->id())->latest()->paginate(20));

        return inertia('Sale/Index', compact('sales'));
    }

    public function create()
    {
        $opportunityId = Oportunity::find(request('opportunityId'));
        $sample = Sample::find(request('sampleId'));

        //optimizacion de datos en vista para reducir el tiempo de carga
        $pre_company_branches = CompanyBranch::with('company.catalogProducts.rawMaterials.storages', 'contacts')->latest()->get();
        $company_branches = $pre_company_branches->map(function ($company_branch) {

            $catalog_products = $company_branch->company->catalogProducts->map(function ($product) {

                $raw_materials = $product->rawMaterials->map(function ($raw_material) {

                    $storages = $raw_material->storages->map(function ($storage) {
                        return [
                            'quantity' => $storage->quantity,
                        ];
                    });

                    return [
                        'name' => $raw_material->name,
                        'pivot' => ['quantity' => $raw_material->pivot->quantity],
                        'storages' => $storages,
                    ];
                });

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'pivot' => ['id' => $product->pivot->id],
                    'raw_materials' => $raw_materials,
                ];
            });


            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'charge' => $contact->charge,
                    'additional_emails' => $contact->additional_emails,
                    'additional_phones' => $contact->additional_phones,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'important_notes' => $company_branch->important_notes,
                'contacts' => $contacts,
                'catalog_products' => $catalog_products,
            ];
        });


        return inertia('Sale/Create', compact('company_branches', 'opportunityId', 'sample'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_company' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
            'order_via' => 'nullable',
            'tracking_guide' => 'nullable',
            'notes' => 'nullable',
            'is_high_priority' => 'boolean',
            'is_sale_production' => 'boolean',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1',
            'partialities' => 'nullable'
        ]);

        $sale = Sale::create($request->except('products') + ['user_id' => auth()->id()]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $sale->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } elseif (app()->environment() === 'production') {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de venta / stock', 'sales.index'));
        }

        // store media
        $sale->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('oce'));

        foreach ($request->products as $product) {
            $cpcs = CatalogProductCompanySale::create($product + ['sale_id' => $sale->id]);

            // guardar en producto ordenado producto terminado disponible usado (siempre y cuando sea orden venta y no stock)
            if ($request->is_sale_production) {
                $finishedQuantityAvailable = $cpcs->catalogProductCompany->catalogProduct->storages[0]->quantity ?? 0;

                $finishedProductUsed = min($finishedQuantityAvailable, $cpcs->quantity);

                // guardar cantidad que se usó de producto terminado
                if ($finishedProductUsed > 0) {
                    $cpcs->update(['finished_product_used' => $finishedProductUsed]);
                }
            }
        }

        event(new RecordCreated($sale));
    }

    public function show($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['user:id,name', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions.operator:id,name', 'comments.user'], 'productions' => ['user:id,name', 'operator:id,name']])->find($sale_id));
        $pre_sales = Sale::latest()->get();
        $sales = $pre_sales->map(function ($sale) {
            $prefix = $sale->is_sale_production ? 'OV-' : 'OS-';
            return [
                'id' => $sale->id,
                'folio' => $prefix . str_pad($sale->id, 4, "0", STR_PAD_LEFT),
            ];
        });

        return inertia('Sale/Show', compact('sale', 'sales'));
    }

    public function edit(Sale $sale)
    {
        $sale = Sale::find($sale->id);
        $catalog_products_company_sale = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')->where('sale_id', $sale->id)->get();
        $media = $sale->getMedia('oce')->all();

        //optimizacion de datos en vista para reducir el tiempo de carga
        $pre_company_branches = CompanyBranch::with('company.catalogProducts.rawMaterials.storages', 'contacts')->latest()->get();
        $company_branches = $pre_company_branches->map(function ($company_branch) {

            $catalog_products = $company_branch->company->catalogProducts->map(function ($product) {

                $raw_materials = $product->rawMaterials->map(function ($raw_material) {

                    $storages = $raw_material->storages->map(function ($storage) {
                        return [
                            'quantity' => $storage->quantity,
                        ];
                    });

                    return [
                        'name' => $raw_material->name,
                        'pivot' => ['quantity' => $raw_material->pivot->quantity],
                        'storages' => $storages,
                    ];
                });

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'pivot' => ['id' => $product->pivot->id],
                    'raw_materials' => $raw_materials,
                ];
            });


            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'charge' => $contact->charge,
                    'additional_emails' => $contact->additional_emails,
                    'additional_phones' => $contact->additional_phones,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'important_notes' => $company_branch->important_notes,
                'contacts' => $contacts,
                'catalog_products' => $catalog_products,
            ];
        });

        return inertia('Sale/Edit', compact('company_branches', 'sale', 'catalog_products_company_sale', 'media'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'shipping_company' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
            'order_via' => 'nullable',
            'tracking_guide' => 'nullable',
            'invoice' => 'nullable',
            'notes' => 'nullable',
            'is_high_priority' => 'nullable',
            'is_sale_production' => 'boolean',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1',
            'partialities' => 'nullable'
        ]);

        $updatedProductIds = [];
        $sale->update($request->except('products'));


        foreach ($request->products as $product) {
            $productData = $product + ['sale_id' => $sale->id];

            if (isset($product['id'])) {
                // Actualizar la relaci贸n existente en catalogProductCompanySales
                $existingRelation = CatalogProductCompanySale::findOrFail($product['id']);
                $existingRelation->update($productData);
                $updatedProductIds[] = $product['id'];
            } else {
                // Crear una nueva relaci贸n en catalogProductCompanySales
                $new = CatalogProductCompanySale::create($productData);
                $updatedProductIds[] = $new->id;
            }
        }

        // Eliminar los productos que no se actualizaron o crearon en esta solicitud y las producciones asignadas
        $sold_products = CatalogProductCompanySale::where('sale_id', $sale->id)
            ->whereNotIn('id', $updatedProductIds);
        $sold_products->first()?->productions()
            ->delete();
        $sold_products->delete();

        event(new RecordEdited($sale));

        return to_route('sales.index');
    }

    public function updateWithMedia(Request $request, Sale $sale)
    {
        $request->validate([
            'shipping_company' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
            'order_via' => 'nullable',
            'tracking_guide' => 'nullable',
            'invoice' => 'nullable',
            'notes' => 'nullable',
            'is_high_priority' => 'boolean',
            'is_sale_production' => 'boolean',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1',
            'partialities' => 'nullable'
        ]);

        $updatedProductIds = [];
        $sale->update($request->except('products'));

        // media
        $sale->clearMediaCollection();
        $sale->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        foreach ($request->products as $product) {
            $productData = $product + ['sale_id' => $sale->id];

            if (isset($product['id'])) {
                // Actualizar la relaci贸n existente en catalogProductCompanySales
                $existingRelation = CatalogProductCompanySale::findOrFail($product['id']);
                $existingRelation->update($productData);
                $updatedProductIds[] = $product['id'];
            } else {
                // Crear una nueva relaci贸n en catalogProductCompanySales
                $new = CatalogProductCompanySale::create($productData);
                $updatedProductIds[] = $new->id;
            }
        }

        // Eliminar los productos que no se actualizaron o crearon en esta solicitud y las producciones asignadas
        $sold_products = CatalogProductCompanySale::where('sale_id', $sale->id)
            ->whereNotIn('id', $updatedProductIds);
        $sold_products->first()?->productions()
            ->delete();
        $sold_products->delete();

        event(new RecordEdited($sale));

        return to_route('sales.index');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        event(new RecordDeleted($sale));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->sales as $sale) {
            $sale = Sale::find($sale['id']);
            foreach ($sale->productions as $production) {
                $production->catalogProductCompanySale?->delete();
                $production->delete();
            }
            $sale?->delete();

            event(new RecordDeleted($sale));
        }

        return to_route('sales.index');
    }

    public function authorizeOrder(Sale $sale)
    {
        $sale->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        return response()->json(['item' => SaleResource::make($sale)]);
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
                'message' => "Orden clonada: $new_item_folio", 'newItem' => saleResource::make(Sale::with('companyBranch', 'user')->find($clone->id))
            ]);
    }

    public function print($sale_id)
    {
        $sale = SaleResource::make(Sale::with('productions', 'catalogProductCompanySales.catalogProductCompany.catalogProduct.media', 'catalogProductCompanySales.sale.user')->find($sale_id));

        // return $sale;
        return inertia('Sale/Print', compact('sale'));
    }

    public function getUnauthorized()
    {
        $sales = SaleResource::collection(Sale::with('catalogProductCompanySales.catalogProductCompany.catalogProduct.storages')
            ->whereNull('authorized_at')->get());

        return response()->json(['items' => $sales]);
    }

    public function getMatches($query)
    {
        if ($query != 'nullable') {
            $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name'])
                ->latest()
                ->where('id', 'LIKE', "%$query%")
                ->orWhere('created_at', 'LIKE', "%$query%")
                ->orWhere('authorized_at', 'LIKE', "%$query%")
                ->orWhere('promise_date', 'LIKE', "%$query%")
                ->orWhereHas('user', function ($user) use ($query) {
                    $user->where('name', 'LIKE', "%$query%");
                })
                ->orWhereHas('companyBranch', function ($user) use ($query) {
                    $user->where('name', 'LIKE', "%$query%");
                })
                ->get());
        } else {
            $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name'])->latest()->paginate(20));
        }

        return response()->json(['items' => $sales]);
    }


    public function qualityCertificate($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['user:id,name', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions.operator:id,name', 'comments.user'], 'productions' => ['user:id,name', 'operator:id,name']])->find($sale_id));

        // return $sale;
        return inertia('Sale/QualityCertificate', compact('sale'));
    }


    public function fetchFiltered($filter)
    {
        if ( $filter == 'Mis órdenes') {
            $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name'])->where('user_id', auth()->id())->latest()->paginate(20));
            return inertia('Sale/Index', compact('sales'));
        } else {
            $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name'])->latest()->paginate(20));
            return inertia('Sale/IndexAll', compact('sales'));
        }

    }

 

}
