<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProductionCostResource;
use App\Http\Resources\QualityResource;
use App\Http\Resources\SaleResource;
use App\Models\Calendar;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompanySale;
use App\Models\Comment;
use App\Models\Production;
use App\Models\ProductionCost;
use App\Models\Quality;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\StockMovementHistory;
use App\Models\Storage;
use App\Models\User;
use App\Notifications\LowStockToDispatchOrderNotification;
use App\Notifications\MentionInProductionNotification;
use App\Notifications\ProductionCompletedNotification;
use App\Notifications\ScheduleTentativeFinishOrder;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    //Index que contiene producciones solo del usuario autenticado
    public function index()
    {
        //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
        $pre_productions = Sale::with([
            'user:id,name',
            'productions' => ['catalogProductCompanySale:id,catalog_product_company_id,sale_id'
            => ['catalogProductCompany:id,catalog_product_id'
            => ['catalogProduct:id,name']]],
            'companyBranch:id,name',
            'productions.operator:id,name'
        ])
            ->whereHas('productions', function ($query) {
                $query->where('productions.operator_id', auth()->id());
            })->latest()
            ->paginate(70, ['id', 'user_id', 'created_at', 'status', 'is_high_priority', 'company_branch_id', 'is_sale_production', 'authorized_at']);

        // return $pre_productions;
        $productions = $this->processDataIndex($pre_productions);

        return inertia('Production/Index', compact('productions'));
    }

    //Index que contiene todos los registros de produccion
    public function adminIndex()
    {
        // Pagina 8 items por página
        $pre_productions = Sale::with([
            'user:id,name',
            'productions' => ['catalogProductCompanySale:id,catalog_product_company_id,sale_id'
            => ['catalogProductCompany:id,catalog_product_id'
            => ['catalogProduct:id,name']]],
            'companyBranch:id,name',
            'productions.operator:id,name'
        ])
            ->whereHas('productions')
            ->latest()
            ->paginate(50, 
                [
                    'id',
                    'promise_date', 
                    'is_sale_production', 
                    'is_high_priority', 
                    'authorized_at', 
                    'user_id', 
                    'company_branch_id', 
                    'contact_id', 
                    'status', 
                    'created_at'
                ]); // Paginar 10 por página

                // return $pre_productions;
            $productions = $this->processDataIndex($pre_productions);
        return inertia('Production/Admin', compact('productions'));
    }

    public function processDataIndex($pre_productions)
    {
        return $pre_productions->through(function ($sale) {
            $productions = $sale->productions ?? collect();

            $startedCount = $productions->whereNotNull('started_at')->count();
            $notFinishedCount = $productions->whereNull('finished_at')->count();
            $totalProductions = $productions->count();
            $finishedCount = $totalProductions - $notFinishedCount;

            // Determinar estatus de producción
            if (is_null($sale->authorized_at)) {
                $status = ['label' => 'Esperando autorización', 'text-color' => 'text-amber-500'];
            } elseif ($totalProductions === 0) {
                $status = ['label' => 'Autorizado sin orden de producción', 'text-color' => 'text-amber-500'];
            } elseif ($startedCount === 0) {
                $status = ['label' => 'Producción sin iniciar', 'text-color' => 'text-gray-500'];
            } elseif ($notFinishedCount > 0) {
                $status = ['label' => 'Producción en proceso', 'text-color' => 'text-blue-500'];
            } else {
                $status = ['label' => 'Producción terminada', 'text-color' => 'text-green-500'];
            }

            // Obtener nombres únicos de operadores
            $operators = $productions
                ->pluck('operator.name')
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            // Obtener nombres únicos de productos
            $products = $productions
                ->map(function ($item) {
                    return optional($item->catalogProductCompanySale?->catalogProductCompany?->catalogProduct)->name;
                })
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            // Porcentaje de avance
            $percentage = $totalProductions > 0
                ? round(($finishedCount * 100) / $totalProductions, 1)
                : 0;

            // Estado de entrega
            $delivery_status = '--';
            $threeDaysBefore = now()->addDays(3);
            $promiseDate = $sale->promise_date;

            if ($status['label'] !== 'Producción terminada') {
                if ($promiseDate?->greaterThanOrEqualTo($threeDaysBefore)) {
                    $delivery_status = 'Fecha cercana';
                } elseif ($promiseDate > now()) {
                    $delivery_status = 'A tiempo';
                } elseif ($promiseDate < now()) {
                    $delivery_status = 'Fuera de tiempo';
                }
            } else {
                $delivery_status = 'Entregado';
            }

            return [
                'id' => $sale->id,
                'folio' => 'OP-' . str_pad($sale->id, 4, "0", STR_PAD_LEFT),
                'user' => [
                    'id' => $sale->user->id,
                    'name' => $sale->user->name
                ],
                'status' => $status,
                'sale_status' => $sale->status,
                'operators' => $operators,
                'company_branch' => [
                    'id' => $sale->companyBranch->id,
                    'name' => $sale->companyBranch->name
                ],
                'products' => $products,
                'production' => [
                    'percentage' => $percentage . '%',
                    'productions_quantity' => $totalProductions
                ],
                'promise_date' => $promiseDate?->isoFormat('DD MMMM YYYY') ?? '--',
                // 'delivery_status' => $delivery_status, // Descomentar si lo usas en frontend
                'created_at' => $sale->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
                'authorized_at' => $sale->authorized_at?->isoFormat('DD MMM, YYYY h:mm A'),
                'is_sale_production' => $sale->is_sale_production,
            ];
        });
    }

    public function create()
    {
        $operators = User::where('employee_properties->department', 'Producción')->where('is_active', 1)->get();
        $sales = SaleResource::collection(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct.media')->whereNotNull('authorized_at')->whereDoesntHave('productions')->get());
        $is_automatic_assignment = boolval(Setting::where('key', 'AUTOMATIC_PRODUCTION_ASSIGNMENT')->first()->value);
        $production_processes = ProductionCostResource::collection(ProductionCost::all());

        return inertia('Production/Create', compact('operators', 'sales', 'is_automatic_assignment', 'production_processes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productions' => 'array|min:1',
        ]);

        $is_automatic_assignment = Setting::where('key', 'AUTOMATIC_PRODUCTION_ASSIGNMENT')->first()->value;

        foreach ($request->productions as $production) {
            $foreigns = [
                'user_id' => $production['user_id'],
                'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
            ];

            foreach ($production['tasks'] as $task) {
                // Calculate total estimated time for the production
                $totalEstimatedTime = ($task['estimated_time_hours'] * 60) + $task['estimated_time_minutes'];

                // Find suitable employees based on criteria
                $selectedEmployees = $this->findSuitableEmployees($totalEstimatedTime);

                $data = $task + $foreigns;
                if ($is_automatic_assignment) {
                    foreach ($selectedEmployees as $employeeId) {
                        $data['operator_id'] = $employeeId;
                        $data['estimated_time_hours'] = intval(($totalEstimatedTime / count($selectedEmployees)) / 60);
                        $data['estimated_time_minutes'] = round($totalEstimatedTime / count($selectedEmployees)) % 60;

                        $produ = Production::create($data);
                        event(new RecordCreated($produ));
                    }
                } else {
                    $produ = Production::create($data);
                    event(new RecordCreated($produ));
                }
            }

            // sub needed quantities from stock -------------------------------------------------------
            // $cpcs = CatalogProductCompanySale::find($production['catalog_product_company_sale_id']);
            // $raw_materials = $cpcs->catalogProductCompany->catalogProduct->rawMaterials;
            // foreach ($raw_materials as $raw_material) {
            //     $quantity_needed = $raw_material->pivot->quantity * $cpcs->quantity;
            //     $storage = Storage::where('storageable_id', $raw_material->id)->where('storageable_type', 'App\Models\RawMaterial')->first();
            //     $storage->decrement('quantity', $quantity_needed);
            //     StockMovementHistory::Create([
            //         'storage_id' => $storage->id,
            //         'user_id' => auth()->id(),
            //         'type' => 'Salida',
            //         'quantity' => $quantity_needed,
            //         'notes' => 'Salida de material automática por orden de producción',
            //     ]);
            // }
        }

        // actualizar status de la venta
        $sale = Sale::find($request->sale_id);
        $sale->status = 'Producción sin iniciar';
        $sale->save();

        return redirect()->route('productions.index');
    }

    public function show($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['user', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'catalogProductCompany.catalogProduct.rawMaterials.storages.storageable', 'productions' => ['user', 'operator', 'progress'], 'comments.user']])->find($sale_id));
        $sales = Sale::with('companyBranch:id,name')->latest()->get(['id', 'company_branch_id']);
        $qualities = QualityResource::collection(Quality::with('supervisor:id,name')->where('production_id', $sale->id)->get());

        return inertia('Production/Show', compact('sale', 'sales', 'qualities'));
    }

    public function edit($sale_id)
    {
        $operators = User::where('employee_properties->department', 'Producción')->where('is_active', 1)->get();
        $sale = SaleResource::make(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct', 'productions')->find($sale_id));
        $production_processes = ProductionCostResource::collection(ProductionCost::all());

        // return $sale;
        return inertia('Production/Edit', compact('operators', 'sale', 'production_processes'));
    }

    // public function update(Request $request, $sale_id)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'productions' => 'array|min:1',
    //     ]);

    //     $sale = Sale::find($sale_id);

    //     foreach ($request->productions as $production) {
    //         $foreigns = [
    //             'user_id' => $production['user_id'],
    //             'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
    //         ];

    //         foreach ($production['tasks'] as $task) {
    //             $data = $task + $foreigns;

    //             // Buscar la producción existente por el ID
    //             $existingProduction = Production::find($data['id'] ?? null);

    //             if ($existingProduction) {
    //                 // Actualizar los datos de la producción existente
    //                 $existingProduction->update($data);
    //                 event(new RecordEdited($existingProduction));
    //             } else {
    //                 // Si no existe, crear una nueva producción
    //                 $prod = Production::create($data);
    //                 event(new RecordEdited($prod));
    //             }
    //         }
    //     }

    //     return to_route('productions.index');
    // }

    public function update(Request $request, $sale_id)
    {
        $request->validate([
            'productions' => 'array|min:1',
        ]);

        $sale = Sale::find($sale_id);

        // Obtener los IDs de producciones existentes en la orden de venta
        $existingProductionIds = $sale->productions->pluck('id')->toArray();

        foreach ($request->productions as $production) {
            $foreigns = [
                'user_id' => $production['user_id'],
                'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
            ];

            foreach ($production['tasks'] as $task) {
                $data = $task + $foreigns;

                // Buscar la producción existente por el ID
                $existingProduction = Production::find($data['id'] ?? null);

                if ($existingProduction) {
                    // Actualizar los datos de la producción existente
                    $existingProduction->update($data);
                    event(new RecordEdited($existingProduction));

                    // Eliminar el ID de la producción existente de la lista
                    unset($existingProductionIds[array_search($existingProduction->id, $existingProductionIds)]);
                } else {
                    // Si no existe, crear una nueva producción
                    $prod = Production::create($data);
                    event(new RecordEdited($prod));
                }
            }
        }

        // Eliminar las producciones que no se actualizaron o crearon
        Production::destroy($existingProductionIds);

        return to_route('productions.show', $sale_id);
    }



    public function destroy(Production $production)
    {
        //
    }

    // methods
    public function massiveDelete(Request $request)
    {
        foreach ($request->sales as $sale) {
            $sale = Sale::find($sale['id']);
            foreach ($sale->productions as $production) {
                $production->delete();

                event(new RecordDeleted($production));
            }
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function print($productions)
    {
        $ordered_products = CatalogProductCompanySale::with(['catalogProductCompany.catalogProduct.media', 'productions' => ['operator:id,name', 'user:id,name'], 'sale' => ['user:id,name', 'companyBranch']])->whereIn('id', json_decode($productions))->get();
        $folio = 'OP-' . str_pad($ordered_products[0]->sale_id, 4, "0", STR_PAD_LEFT);

        // return $ordered_products;
        return inertia('Production/PrintTemplate', compact('ordered_products', 'folio'));
    }

    public function changeStatus(Request $request, Production $production)
    {
        $sale = $production->catalogProductCompanySale->sale;

        if (!$production->started_at) {
            $prefix = $production->catalogProductCompanySale->sale->is_sale_production ? 'OP-' : 'OS-';
            $production_folio = $prefix . str_pad($production->catalogProductCompanySale->sale->id, 4, "0", STR_PAD_LEFT);
            $production->update(['started_at' => now()]);
            $message = 'Se ha registrado el inicio';
            $cpcs = CatalogProductCompanySale::find($production['catalog_product_company_sale_id']);
            $convertedDate = $cpcs->getEstimatedCompletionDate()->format('Y-m-d');
            $users = User::whereIn('id', [2, 3, 37])->get(); //sherman, maribel y Adriana
            foreach ($users as $user) {
                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => "Finalización tentativa de $production_folio",
                    'repeater' => 'No se repite',
                    'description' => "Recordatorio automático para seguimiento",
                    'status' => 'Pendiente',
                    'start_date' => $convertedDate,
                    'start_time' => '8:00 AM',
                    'user_id' => auth()->id(),
                ]);

                $user->notify(new ScheduleTentativeFinishOrder($reminder));
            }
        } else if ($production->started_at->diffInMinutes(now()) > 4) {
            $prefix = $production->catalogProductCompanySale->sale->is_sale_production ? 'OP-' : 'OS-';
            $production_folio = $prefix . str_pad($production->catalogProductCompanySale->sale->id, 4, "0", STR_PAD_LEFT);
            $request->validate([
                'good_units' => 'nullable|numeric|min:0',
                'scrap' => 'nullable|numeric|min:0',
                'reason' => 'nullable|string|max:800',
                'packages' => 'nullable|array',
            ]);
            $production->update([
                'finished_at' => now(),
                'has_low_stock' => false,
                'is_paused' => 0,
                'scrap' => $request->scrap,
                'scrap_reason' => $request->reason,
                'good_units' => $request->good_units,
                'packages' => $request->packages,
                'supervision' => $request->supervision,
            ]);

            // si ya se finalizó completamente
            if ($sale->getStatus()['label'] == 'Producción terminada') {
                $cpcs = CatalogProductCompanySale::find($production['catalog_product_company_sale_id']);
                $raw_materials = $cpcs->catalogProductCompany->catalogProduct->rawMaterials;
                // descontar materia prima utilizada para la producción
                foreach ($raw_materials as $raw_material) {
                    $quntity_produced = $cpcs->quantity - $cpcs->finished_product_used;
                    $quantity_needed = intval($raw_material->pivot->quantity * $quntity_produced);
                    $storage = Storage::where('storageable_id', $raw_material->id)->where('storageable_type', 'App\Models\RawMaterial')->first();
                    $updated_quantity =  $storage->quantity - $quantity_needed;
                    $storage->update(['quantity' => $updated_quantity]);
                    // crear historial de movimiento si se produjo al menos una unidad
                    if ($quantity_needed) {
                        StockMovementHistory::Create([
                            'storage_id' => $storage->id,
                            'user_id' => auth()->id(),
                            'type' => 'Salida',
                            'quantity' => $quantity_needed,
                            'notes' => 'Salida de material automática por orden de producción terminada ' .  $production_folio,
                        ]);
                    }
                }

                // agregar a almacen de producto terminado si es orden de stock
                if (!$production->catalogProductCompanySale->sale->is_sale_production) {
                    $catalog_product = $cpcs->catalogProductCompany->catalogProduct;
                    // Buscar el registro existente de storage para producto-terminado
                    $existingStorage = $catalog_product->storages()
                        ->where('type', 'producto-terminado')
                        ->first();

                    if ($existingStorage) {
                        // Si existe, incrementar la cantidad existente
                        $existingStorage->increment('quantity', $cpcs->quantity);
                    } else {
                        // Si no existe, crear un nuevo registro de storage
                        $catalog_product->storages()->create([
                            'quantity' => $cpcs->quantity,
                            'location' => 'Por definir',
                            'type' => 'producto-terminado',
                        ]);
                        $existingStorage = $catalog_product->storages()
                            ->where('type', 'producto-terminado')
                            ->first();
                    }
                    StockMovementHistory::Create([
                        'storage_id' => $existingStorage->id,
                        'user_id' => auth()->id(),
                        'type' => 'Entrada',
                        'quantity' => $cpcs->quantity,
                        'notes' => 'Entrada de producto terminado desde orden de stock ' .  $production_folio,
                    ]);
                }

                // rebajar o eliminar cantidad en almacen de producto terminado en caso de que se haya usado
                if ($cpcs->finished_product_used > 0) {
                    $finished_product = $cpcs->catalogProductCompany->catalogProduct->storages[0];
                    if ($finished_product->quantity > $cpcs->quantity) {
                        $finished_product->decrement('quantity', $cpcs->quantity);
                    } else {
                        $finished_product->delete();
                    }
                }
            }

            // notificar a jefe de producción
            $user = User::where('employee_properties->job_position', 'Jefe de producción')->first();

            //si se encuentra jefe de producción se hace la notificación, si no se encuentra no se manda
            if ($user) {
                $user->notify(
                    new ProductionCompletedNotification(
                        $production->catalogProductCompanySale->catalogProductCompany->catalogProduct->name,
                        'OP-' . str_pad($production->catalogProductCompanySale->sale->id, 4, "0", STR_PAD_LEFT),
                        "",
                        'production'
                    )
                );
            }

            $message = $production->catalogProductCompanySale->sale->is_sale_production
                ? 'Se ha registrado el final'
                : 'Se ha guardado automáticamente la cantidad en almacén de producto terminado.';
            $production = Production::with(['operator', 'user'])->find($production->id);
        } else {
            $production = null;
            $message = "No se puede iniciar y finalizar la tarea de inmediato. Al iniciar, empiezas la tarea y marcas el final cuando la completas realmente. Esto permite monitorear la producción en tiempo real.";
        }

        // actualizar status para evitar calcularlo cada que se requiera solicitar
        $sale->status = $sale->getStatus()['label'];
        $sale->save();

        return response()->json(['message' => $message, 'item' => $production]);
    }

    public function changeStockStatus(Production $production)
    {
        $production->update([
            'has_low_stock' => !$production->has_low_stock,
        ]);

        if ($production->has_low_stock) {
            // notificar a compras
            $operator_name = $production->operator->name;
            $product = $production->catalogProductCompanySale->catalogProductCompany->catalogProduct->name;
            $folio = 'OP-' . str_pad($production->catalogProductCompanySale->sale->id, 4, "0", STR_PAD_LEFT);
            $route = route('productions.show', $production->catalogProductCompanySale->sale->id);
            $users = User::where('is_active', true)->get();
            $users->each(function ($user) use ($operator_name, $product, $folio, $route) {
                if ($user->can('Crear ordenes de compra')) {
                    $user->notify(new LowStockToDispatchOrderNotification($operator_name, $product, $folio, $route));
                }
            });
        }

        return response()->json(['message' => 'Se ha cambiado el status de materia prima']);
    }

    public function continueProduction(Production $production)
    {
        $production->progress->last()->update(['finished_at' => now()->toDateTimeString()]);
        $production->update(['is_paused' => 0]);

        return response()->json(['message' => 'Producción reanudada']);
    }

    public function comment(Request $request, CatalogProductCompanySale $cpcs)
    {
        $comment = new Comment([
            'body' => $request->comment,
            'user_id' => auth()->id(),
        ]);

        $cpcs->comments()->save($comment);

        $mentions = $request->mentions;
        foreach ($mentions as $mention) {
            $user = User::find($mention['id']);
            $user->notify(new MentionInProductionNotification($cpcs));
        }

        event(new RecordCreated($comment));

        return response()->json(['item' => $comment->fresh('user')]);
    }

    // private methods
    private function findSuitableEmployees($totalEstimatedTime)
    {
        $employees = User::where('is_active', 1)->where('employee_properties->department', 'Producción')->whereIn('id', [4, 6, 8, 14, 27])->get();

        // Calculate required employee count based on production time
        $requiredEmployeeCount = ceil($totalEstimatedTime / 240); // 240 minutes per employee

        // If required employee count is greater than suitable employees, assign all suitable employees
        if ($requiredEmployeeCount >= $employees->count()) {
            return $employees->pluck('id')->toArray();
        }

        // Select the most optimal employees based on their total estimated time
        $sortedEmployees = $employees->sortBy(function ($employee) {
            return $employee->getTotalEstimatedTime();
        });

        return $sortedEmployees->take($requiredEmployeeCount)->pluck('id')->toArray();
    }

    public function showTravelerTemplate(CatalogProductCompanySale $cpcs)
    {
        return inertia('Production/TravelerTemplate', compact('cpcs'));
    }

    public function generateBoxLabel(Request $request)
    {
        return inertia('Production/BoxLabel', ['data' => $request->data]);
    }

    public function generateLocalBoxLabel(Request $request)
    {
        return inertia('Production/LocalBoxLabel', ['data' => $request->data]);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $pre_productions = Sale::with('user', 'productions.catalogProductCompanySale.catalogProductCompany.catalogProduct', 'companyBranch', 'productions.operator')
            ->whereHas('productions')
            ->where('id', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(200);

        $productions = $this->processDataIndex($pre_productions);

        return response()->json(['items' => $productions], 200);
    }

    public function fetchCatalogProductShippingRates(CatalogProduct $catalog_product)
    {
        $catalog_product->load('shippingRates');

        return response()->json(['item' => $catalog_product]);
    }

    public function fetchSaleInfo(Sale $sale)
    {
        return response()->json(['item' => $sale]);
    }
}
