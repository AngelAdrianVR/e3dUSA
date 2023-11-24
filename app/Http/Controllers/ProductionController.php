<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProductionCostResource;
use App\Http\Resources\SaleResource;
use App\Models\CatalogProductCompanySale;
use App\Models\Comment;
use App\Models\Production;
use App\Models\ProductionCost;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\StockMovementHistory;
use App\Models\Storage;
use App\Models\User;
use App\Notifications\MentionInProductionNotification;
use App\Notifications\MentionNotification;
use App\Notifications\ProductionCompletedNotification;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    public function index()
    {
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de produccion todas')) {
            // $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale.catalogProductCompany.catalogProduct', 'companyBranch')->whereHas('productions')->latest()->get());
            //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
            $pre_productions = Sale::with('user', 'productions.catalogProductCompanySale.catalogProductCompany.catalogProduct', 'companyBranch', 'productions.operator')->whereHas('productions')->latest()->get();
            $productions = $pre_productions->map(function ($production) {
                $hasStarted = $production->productions?->whereNotNull('started_at')->count();
                $hasNotFinished = $production->productions?->whereNull('finished_at')->count();

                if ($production->authorized_at == null) {
                    $status = [
                        'label' => 'Esperando autorización',
                        'text-color' => 'text-amber-500',
                    ];
                } elseif ($production->productions) {
                    if (!$hasStarted) {
                        $status = [
                            'label' => 'Producción sin iniciar',
                            'text-color' => 'text-gray-500',
                        ];
                    } elseif ($hasStarted && $hasNotFinished) {
                        $status = [
                            'label' => 'Producción en proceso',
                            'text-color' => 'text-blue-500',
                        ];
                    } else {
                        $status = [
                            'label' => 'Producción terminada',
                            'text-color' => 'text-green-500',
                        ];
                    }
                } else {
                    $status = [
                        'label' => 'Autorizado sin orden de producción',
                        'text-color' => 'text-amber-500',
                    ];
                }
                //Guarda en un array "productionNames" los nombres de los operadores de esa orden de produccion
                $productionNames = [];
                foreach ($production->productions as $productionItem) {
                    $productionNames[] = $productionItem['operator']['name'];
                }
                $uniqueProductionNames = array_unique($productionNames);
                $operators = implode(', ', $uniqueProductionNames);

                //Guarda en un array "productNames" los nombres de los productos de la orden
                // $productNames = [];
                // foreach ($production->productions as $product) {
                //     if (
                //         isset($product['catalog_product_company_sale']) &&
                //         isset($product['catalog_product_company_sale']['catalog_product_company']) &&
                //         isset($product['catalog_product_company_sale']['catalog_product_company']['catalog_product'])
                //     ) {
                //         $productName = $product['catalog_product_company_sale']['catalog_product_company']['catalog_product']['name'];
                //         $productNames[] = $productName;
                //     }

                // }

                // $uniqueProductNames = array_unique($productNames);
                // $products = implode(', ', $uniqueProductNames);

                //Calulo del porcentage de avance de producción.
                // Contador para llevar el registro de cuántas producciones tienen fecha en "finished_at"
                $finishedCount = 0;
                foreach ($production->productions as $productionItem) {
                    if ($productionItem->finished_at != null) {
                        $finishedCount++;
                    }
                }

                // Calcular el porcentaje
                $percentage = $finishedCount > 0 ? (100 / count($production->productions)) * $finishedCount : 0;


                return [
                    'id' => $production->id,
                    'folio' => 'OP-' . str_pad($production->id, 4, "0", STR_PAD_LEFT),
                    'user' => [
                        'id' => $production->user->id,
                        'name' => $production->user->name
                    ],
                    'status' => $status,
                    'operators' => $operators, //nombres de operadores asignados
                    'company_branch' => [
                        'id' => $production->companyBranch->id,
                        'name' => $production->companyBranch->name
                    ],
                    // 'products' => $production->productions, no me arroja ningun nombre
                    'production' => [
                        'percentage' => $percentage . '%',
                        'productions_quantity' => count($production->productions)
                    ],
                    'created_at' => $production->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
                ];
            });
            // return $pre_productions;
            return inertia('Production/Admin', compact('productions'));
        } elseif (auth()->user()->can('Ordenes de produccion personal')) {
            $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale.catalogProductCompany.catalogProduct', 'companyBranch')->whereHas('productions')->where('user_id', auth()->id())->latest()->get());
            return inertia('Production/Index', compact('productions'));
        } else {
            $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale.catalogProductCompany.catalogProduct', 'companyBranch')->whereHas('productions', function ($query) {
                $query->where('productions.operator_id', auth()->id());
            })->latest()->get());
            return inertia('Production/Operator', compact('productions'));
        }
    }

    public function create()
    {
        $operators = User::where('employee_properties->department', 'Producción')->where('is_active', 1)->get();
        $sales = SaleResource::collection(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct')->whereNotNull('authorized_at')->whereDoesntHave('productions')->get());
        $is_automatic_assignment = boolval(Setting::where('key', 'AUTOMATIC_PRODUCTION_ASSIGNMENT')->first()->value);
        $production_processes = ProductionCostResource::collection(ProductionCost::all());

        // return $production_processes;

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

        return redirect()->route('productions.index');
    }

    public function show($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['user', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions' => ['operator', 'progress'], 'comments.user'], 'productions' => ['user', 'operator', 'progress']])->find($sale_id));
        $pre_sales = Sale::latest()->get();
        $sales = $pre_sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'folio' => 'OV-' . str_pad($sale->id, 4, "0", STR_PAD_LEFT),
                'company_name' => $sale->companyBranch?->name,
            ];
        });
        // $sales = SaleResource::collection(Sale::with(['user', 'contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions' => ['operator', 'progress'], 'comments.user'], 'productions' => ['user', 'operator', 'progress']])->whereHas('productions')->get());

        return inertia('Production/Show', compact('sale', 'sales'));
    }

    public function edit($sale_id)
    {
        $operators = User::where('employee_properties->department', 'Producción')->get();
        $sale = SaleResource::make(Sale::with('companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct', 'productions')->find($sale_id));
        $production_processes = ProductionCostResource::collection(ProductionCost::all());

        // return $sale;
        return inertia('Production/Edit', compact('operators', 'sale', 'production_processes'));
    }

    // public function update(Request $request, $sale_id) //actualiza sin tener que volver a crear los registros. No sirve 
    // {
    //     $request->validate([
    //         'productions' => 'array|min:1',
    //     ]);

    //     $sale = Sale::find($sale_id);

    //     // return $request->editedTaskIndexes;

    //     foreach ($request->productions as $production) {
    //         $foreigns = [
    //             'user_id' => $production['user_id'],
    //             'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
    //         ];

    //         foreach ($production['tasks'] as $taskIndex => $task) {
    //             $data = $task + $foreigns;

    //             if (is_array($request->editedTaskIndexes) && in_array($taskIndex, $request->editedTaskIndexes)) {

    //                 $sale->productions[$taskIndex]->update($data);
    //                 //Production::create($data);

    //             } else {
    //                 $sale->productions[$taskIndex]->update($data);
    //                 //Production::create($data);
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
        $sale->productions()->delete();

        foreach ($request->productions as $production) {
            $foreigns = [
                'user_id' => $production['user_id'],
                'catalog_product_company_sale_id' => $production['catalog_product_company_sale_id']
            ];

            foreach ($production['tasks'] as $task) {
                $data = $task + $foreigns;

                $prod = Production::create($data);
                event(new RecordEdited($prod));
            }
        }


        return to_route('productions.index');
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
        $ordered_products = CatalogProductCompanySale::with(['catalogProductCompany.catalogProduct.media', 'productions' => ['operator', 'user'], 'sale' => ['user', 'companyBranch']])->whereIn('id', json_decode($productions))->get();
        $folio = 'OP-' . str_pad($ordered_products[0]->sale_id, 4, "0", STR_PAD_LEFT);
        return inertia('Production/PrintTemplate', compact('ordered_products', 'folio'));
    }

    public function changeStatus(Request $request, Production $production)
    {
        if (!$production->started_at) {
            $production->update(['started_at' => now()]);
            $message = 'Se ha registrado el inicio';
        } else {
            $request->validate([
                'scrap' => 'required|numeric|min:0'
            ]);
            $production->update([
                'finished_at' => now(), 'is_paused' => 0,
                'scrap' => $request->scrap,
            ]);

            $user = User::where('employee_properties->job_position', 'Jefe de producción')->first();
            $user->notify(
                new ProductionCompletedNotification(
                    $production->catalogProductCompanySale->catalogProductCompany->catalogProduct->name,
                    'OP-' . str_pad($production->catalogProductCompanySale->sale->id, 4, "0", STR_PAD_LEFT),
                    "",
                    'production'
                )
            );

            $message = 'Se ha registrado el final';
        }

        $production = Production::with(['operator', 'user'])->find($production->id);

        return response()->json(['message' => $message, 'item' => $production]);
    }

    public function changeStockStatus(Production $production)
    {
        $production->update([
            'has_low_stock' => !$production->has_low_stock,
        ]);

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
}
