<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\SaleResource;
use App\Models\CatalogProductCompanySale;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Sale;
use App\Models\StockMovementHistory;
use App\Models\Storage;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use Illuminate\Http\Request;


class SaleController extends Controller
{

    public function index()
    {
        $sales = SaleResource::collection(Sale::with('companyBranch', 'user')->latest()->get());

        // return $sales;

        return inertia('Sale/Index', compact('sales'));
    }


    public function create()
    {
        $data = request('data');

        $company_branches = CompanyBranch::with('company.catalogProducts.rawMaterials.storages', 'contacts')->latest()->get();

        return inertia('Sale/Create', compact('company_branches', 'data'));
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
        $can_authorize = auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $sale->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de venta', 'sales.index'));
        }

        // store media
        $sale->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('oce'));

        foreach ($request->products as $product) {
            $cpcs = CatalogProductCompanySale::create($product + ['sale_id' => $sale->id]);

            // sub needed quantities from stock
            $raw_materials = $cpcs->catalogProductCompany->catalogProduct->rawMaterials;
            foreach ($raw_materials as $raw_material) {
                $quantity_needed = $raw_material->pivot->quantity * $cpcs->quantity;
                $storage = Storage::where('storageable_id', $raw_material->id)->where('storageable_type', 'App\Models\RawMaterial')->first();
                $storage->decrement('quantity', $quantity_needed);
                StockMovementHistory::Create([
                    'storage_id' => $storage->id,
                    'user_id' => auth()->id(),
                    'type' => 'Salida',
                    'quantity' => $quantity_needed,
                    'notes' => 'Salida de material automática por orden de producción',
                ]);
            }
        }

        event(new RecordCreated($sale));

        // return to_route('sales.index');
    }

    public function show(Sale $sale)
    {
        $sales = SaleResource::collection(Sale::with(['user', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions.operator'], 'productions' => ['user', 'operator']])->latest()->get());

        // return $sales;
        return inertia('Sale/Show', compact('sale', 'sales'));
    }

    public function edit(Sale $sale)
    {
        $sale = Sale::find($sale->id);
        $catalog_products_company_sale = CatalogProductCompanySale::with('catalogProductCompany')->where('sale_id', $sale->id)->get();
        $company_branches = CompanyBranch::with('company.catalogProducts', 'contacts')->get();
        $media = $sale->getMedia('oce')->all();

        return inertia('Sale/Edit', compact('company_branches', 'sale', 'catalog_products_company_sale', 'media'));
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
                'message' => "Orden de venta clonada: $new_item_folio", 'newItem' => saleResource::make(Sale::with('companyBranch', 'user')->find($clone->id))
            ]);
    }

    public function print($sale_id)
    {
        $sale = SaleResource::make(Sale::with('productions', 'catalogProductCompanySales.catalogProductCompany.catalogProduct.media', 'catalogProductCompanySales.sale.user')->find($sale_id));

        // return $sale;
        return inertia('Sale/Print', compact('sale'));
    }
}
