<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\SaleResource;
use App\Models\Calendar;
use App\Models\CatalogProductCompanySale;
use App\Models\CompanyBranch;
use App\Models\Oportunity;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Sample;
use App\Models\User;
use App\Notifications\ApprovalOkNotification;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\ReminderPartialitiesNotification;
use App\Notifications\SchedulePartialitiesReminder;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = SaleResource::collection(Sale::with(['companyBranch:id,name', 'user:id,name', 'invoices'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20));

        // return $sales;
        return inertia('Sale/Index', compact('sales'));
    }

    public function create()
    {
        $opportunityId = Oportunity::find(request('opportunityId'));
        $sample = Sample::find(request('sampleId'));
        $quotes = Quote::with('CompanyBranch:id,name')->whereNotNull('authorized_at')->whereNull('sale_id')->latest()->get(['id','company_branch_id'])->take(100);

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
                'company_id' => $company_branch->company_id,
                'name' => $company_branch->name,
                'important_notes' => $company_branch->important_notes,
                'contacts' => $contacts,
                'catalog_products' => $catalog_products,
            ];
        });

        return inertia('Sale/Create', compact('company_branches', 'opportunityId', 'sample', 'quotes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'shipping_company' => 'nullable',
            // 'order_via' => 'nullable',
            // 'tracking_guide' => 'nullable',
            'quote_id' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
            'is_high_priority' => 'boolean',
            'is_sale_production' => 'boolean',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1',
            'partialities' => 'nullable|array'
        ]);

        // // Eliminar de productos seleccionados en parcialidades aquellos que no se fueron en el envio
        // $partialities = $request->input('partialities');
        // // Iterar sobre las parcialidades y filtrar los productos seleccionados
        // foreach ($partialities as &$partiality) {
        //     // Filtrar los productos cuyo 'selected' sea true
        //     $partiality['productsSelected'] = array_filter($partiality['productsSelected'], function ($product) {
        //         return $product['selected'] === true;
        //     });

        //     // Eliminar la clave 'selected' de cada producto
        //     $partiality['productsSelected'] = array_map(function ($product) {
        //         unset($product['selected']); // Eliminar la clave 'selected'
        //         return $product;
        //     }, $partiality['productsSelected']);
        // }

        $sale = Sale::create($request->except('products') + ['user_id' => auth()->id()]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $sale->update(['status' => 'Autorizado. Sin orden de producción', 'authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } elseif (app()->environment() === 'production') {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de venta / stock', 'sales.index'));
        }

        // store media
        // Guardar el archivo de acuse de logística en la colección 'acuse'
        if ($request->hasFile('acuse')) {
            $sale->addMediaFromRequest('acuse')->toMediaCollection('acuse');
        }
        // Guardar los archivos en la colección 'OCE'
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $uploadedFile) {
                $sale->addMedia($uploadedFile)->toMediaCollection('oce');
            }
        }
        // Guardar los archivos en la colección 'anotherFiles'
        if ($request->hasFile('anotherFiles')) {
            foreach ($request->file('anotherFiles') as $uploadedFile) {
                $sale->addMedia($uploadedFile)->toMediaCollection('anotherFiles');
            }
        }

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

        // cambiar || por && si se requiere crear el recordatorio solo para parcialidades mayores a 1
        if (collect($request->partialities)->count() > 1 && $request->create_calendar_task) {
            foreach ($request->partialities as $index => $partiality) {
                // if ($index > 0) { // Omite el primer elemento
                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => 'Envío de parcialidad N°' . ($index + 1) . ' de OV-' . $sale->id,
                    'repeater' => 'No se repite',
                    'description' => 'Revisar envío de parcialidad agendada automáticamente para la OV-' . $sale->id . '. Se te recordará 3 días antes de la fecha agendada',
                    'status' => 'Pendiente',
                    'start_date' => $partiality['promise_date'],
                    'start_time' => '8:00 AM',
                    'user_id' => auth()->id(),
                ]);

                $seller = User::find($sale->user_id);
                $seller->notify(new SchedulePartialitiesReminder($reminder));
                // }
            }
        }

        // Agregar el id de la venta a la cotizacion.
        if ($request->quote_id) {
            $quote = Quote::find($request->quote_id);
            $quote->update(['sale_id' => $sale->id]);
        }
    }

    public function show($sale_id)
    {
        // $sale = Sale::find($sale_id, ['id', 'is_sale_production', 'authorized_at', 'user_id']);
        $sale = SaleResource::make(Sale::with(['invoices', 'user:id,name', 'contact', 'media', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions.operator:id,name', 'comments.user'], 'productions' => ['user:id,name', 'operator:id,name']])->find($sale_id));
        $sales = Sale::latest()->get(['id']);

        // return $sale;
        return inertia('Sale/Show', compact('sale', 'sales'));
    }

    public function edit(Sale $sale)
    {
        $sale = Sale::find($sale->id);
        $catalog_products_company_sale = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')->where('sale_id', $sale->id)->get();
        $mediaOCE = $sale->getMedia('oce')->all();
        $media_oce_url = $sale->getFirstMediaUrl('oce');
        $acuse = $sale->getMedia('acuse')->all();
        $media_acuse_url = $sale->getFirstMediaUrl('acuse');
        $anotherFiles = $sale->getMedia('anotherFiles')->all();
        $media_anotherFiles_url = $sale->getFirstMediaUrl('anotherFiles');

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

        return inertia('Sale/Edit', compact('company_branches', 'sale', 'catalog_products_company_sale', 'mediaOCE', 'media_oce_url', 'acuse', 'media_acuse_url', 'anotherFiles', 'media_anotherFiles_url'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            // 'shipping_company' => 'nullable',
            // 'order_via' => 'nullable',
            // 'tracking_guide' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
            'invoice' => 'nullable',
            'notes' => 'nullable',
            'is_high_priority' => 'nullable',
            'is_sale_production' => 'boolean',
            'company_branch_id' => 'required|numeric|min:1',
            'contact_id' => 'required|numeric|min:1',
            'products' => 'array|min:1',
            'partialities' => 'array|min:1',
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

        // cambiar || por && si se requiere crear el recordatorio solo para parcialidades mayores a 1
        if (collect($request->partialities)->count() > 1 && $request->create_calendar_task) {
            foreach ($request->partialities as $index => $partiality) {
                // if ($index > 0) { // Omite el primer elemento
                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => 'Envío de parcialidad N°' . ($index + 1) . ' de OV-' . $sale->id,
                    'repeater' => 'No se repite',
                    'description' => 'Revisar envío de parcialidad agendada automáticamente para la OV-' . $sale->id . '. Se te recordará 3 días antes de la fecha agendada',
                    'status' => 'Pendiente',
                    'start_date' => $partiality['promise_date'],
                    'start_time' => '8:00 AM',
                    'user_id' => auth()->id(),
                ]);

                $seller = User::find($sale->user_id);
                $seller->notify(new SchedulePartialitiesReminder($reminder));
                // }
            }
        }

        // return to_route('sales.index');
        return to_route('sales.show', $sale->id);
    }

    public function updateWithMedia(Request $request, Sale $sale)
    {
        $request->validate([
            // 'shipping_company' => 'nullable',
            // 'order_via' => 'nullable',
            // 'tracking_guide' => 'nullable',
            'freight_cost' => 'nullable|numeric|min:0',
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

        // media ---------------------------------------------------------
        // Guardar el archivo de acuse de logística en la colección 'acuse'
        if ($request->hasFile('acuse')) {
            $sale->clearMediaCollection('acuse');
            $sale->addMediaFromRequest('acuse')->toMediaCollection('acuse');
        }
        // Guardar los archivos en la colección 'OCE'
        if ($request->hasFile('media')) {
            $sale->clearMediaCollection('oce');
            foreach ($request->file('media') as $uploadedFile) {
                $sale->addMedia($uploadedFile)->toMediaCollection('oce');
            }
        }
        // Guardar los archivos en la colección 'anotherFiles'
        if ($request->hasFile('anotherFiles')) {
            $sale->clearMediaCollection('anotherFiles');
            foreach ($request->file('anotherFiles') as $uploadedFile) {
                $sale->addMedia($uploadedFile)->toMediaCollection('anotherFiles');
            }
        }

        foreach ($request->products as $product) {
            $productData = $product + ['sale_id' => $sale->id];

            if (isset($product['id'])) {
                // Actualizar la relacion existente en catalogProductCompanySales
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

        // cambiar || por && si se requiere crear el recordatorio solo para parcialidades mayores a 1
        if (collect($request->partialities)->count() > 1 && $request->create_calendar_task) {
            foreach ($request->partialities as $index => $partiality) {
                // if ($index > 0) { // Omite el primer elemento
                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => 'Envío de parcialidad N°' . ($index + 1) . ' de OV-' . $sale->id,
                    'repeater' => 'No se repite',
                    'description' => 'Revisar envío de parcialidad agendada automáticamente para la OV-' . $sale->id . '. Se te recordará 3 días antes de la fecha agendada',
                    'status' => 'Pendiente',
                    'start_date' => $partiality['promise_date'],
                    'start_time' => '8:00 AM',
                    'user_id' => auth()->id(),
                ]);

                $seller = User::find($sale->user_id);
                $seller->notify(new SchedulePartialitiesReminder($reminder));
                // }
            }
        }

        // return to_route('sales.index');
        return to_route('sales.show', $sale->id);
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
            'status' => 'Autorizado. Sin orden de producción',
        ]);

        // notificar a creador de la orden si quien autoriza no es el mismo usuario
        if (auth()->id() != $sale->user->id) {
            $prefix = $sale->is_sale_production ? 'OV-' : 'OS-';
            $sale_folio = $prefix . str_pad($sale->id, 4, "0", STR_PAD_LEFT);
            $sale->user->notify(new ApprovalOkNotification(
                'Órden de venta/stock',
                $sale_folio,
                'sales',
                route('sales.show', $sale->id)
            ));
        }

        return response()->json(['item' => SaleResource::make($sale)]);
    }

    public function clone(Request $request)
    {
        $sale = Sale::find($request->sale_id);

        $clone = $sale->replicate()->fill([
            'oce_name' => null,
            'authorized_user_name' => null,
            'authorized_at' => null,
            'recieved_at' => null,
            'status' => 'Esperando autorización',
            'user_id' => auth()->id(),
        ]);

        // autorizar en caso de que el usuario que clona tenga el permiso
        $can_authorize = auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin');
        if ($can_authorize) {
            $clone->update(['status' => 'Autorizado. Sin orden de producción', 'authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } elseif (app()->environment() === 'production') {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de venta / stock', 'sales.index'));
        }

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
                'message' => "Orden clonada: $new_item_folio",
                'newItem' => saleResource::make(Sale::with('companyBranch', 'user')->find($clone->id))
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

        return inertia('Sale/QualityCertificate', compact('sale'));
    }


    public function fetchFiltered($filter)
    {
        $withRelations = ['companyBranch:id,name', 'user:id,name', 'invoices'];

        if ($filter == 'Mis órdenes') {
            $sales = SaleResource::collection(
                Sale::with($withRelations)
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->paginate(20)
            );
            return inertia('Sale/Index', compact('sales'));
        } else {
            $sales = SaleResource::collection(
                Sale::with($withRelations)
                    ->latest()
                    ->paginate(20)
            );
            // return $sales;
            return inertia('Sale/IndexAll', compact('sales'));
        }
    }



    public function checkIfHasSale($catalog_product_company_id)
    {
        $catalog_product_company_sale = CatalogProductCompanySale::where('catalog_product_company_id', $catalog_product_company_id)->first();

        if ($catalog_product_company_sale) {
            $has_sale = true;
        } else {
            $has_sale = false;
        }

        return response()->json(compact('has_sale'));
    }

    public function fetchSalesNoInvoices()
    {
        $sales = Sale::latest()
            ->doesntHave('invoices')
            ->where('status', 'Producción terminada')
            ->with(['user:id,name', 'companyBranch:id,name'])
            ->select(['id', 'company_branch_id', 'user_id', 'authorized_at', 'authorized_user_name', 'is_sale_production'])
            ->paginate(30);

        return response()->json(compact('sales'));
    }

    public function getMatchesNoInvoives($query)
    {
        if ($query != 'nullable') {
            $sales = Sale::with(['companyBranch:id,name', 'user:id,name'])
                ->latest()
                ->doesntHave('invoices')
                ->where('id', 'LIKE', "%$query%")
                ->orWhere('promise_date', 'LIKE', "%$query%")
                ->orWhereHas('user', function ($user) use ($query) {
                    $user->where('name', 'LIKE', "%$query%");
                })
                ->orWhereHas('companyBranch', function ($user) use ($query) {
                    $user->where('name', 'LIKE', "%$query%");
                })
                ->select(['id', 'company_branch_id', 'user_id', 'authorized_at', 'authorized_user_name', 'is_sale_production'])
                ->get();
        }

        return response()->json(compact('sales'));
    }
}
