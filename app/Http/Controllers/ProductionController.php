<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProductionResource;
use App\Http\Resources\SaleResource;
use App\Models\CatalogProductCompanySale;
use App\Models\Production;
use App\Models\Sale;
use App\Models\Setting;
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
            $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale', 'companyBranch')->whereHas('productions')->where('user_id', auth()->id())->latest()->get());
            return inertia('Production/Index', compact('productions'));
        } else {
            $productions = SaleResource::collection(Sale::with('user', 'productions.catalogProductCompanySale', 'companyBranch')->whereHas('productions', function ($query) {
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

        return inertia('Production/Create', compact('operators', 'sales', 'is_automatic_assignment'));
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
        }

        return redirect()->route('productions.index');
    }

    public function show($sale_id)
    {
        $sale = SaleResource::make(Sale::with(['contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions' => ['operator', 'progress']], 'productions' => ['user', 'operator', 'progress']])->find($sale_id));
        $sales = SaleResource::collection(Sale::with(['contact', 'companyBranch.company', 'catalogProductCompanySales' => ['catalogProductCompany.catalogProduct.media', 'productions' => ['operator', 'progress']], 'productions' => ['user', 'operator', 'progress']])->whereHas('productions')->get());

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
        // return $ordered_products;
        return inertia('Production/PrintTemplate', compact('ordered_products'));
    }

    public function changeStatus(Production $production)
    {
        if (!$production->started_at) {
            $production->update(['started_at' => now()]);
            $message = 'Se ha registrado el inicio';
        } else {
            $production->update(['finished_at' => now(), 'is_paused' => 0]);
            $message = 'Se ha registrado el final';
        }

        $production = Production::with(['operator', 'user'])->find($production->id);

        return response()->json(['message' => $message, 'item' => $production]);
    }

    public function continueProduction(Production $production)
    {
        $production->progress->last()->update(['finished_at' => now()->toDateTimeString()]);
        $production->update(['is_paused' => 0]);

        return response()->json(['message' => 'Producción reanudada']);
    }

    // private methods
    private function findSuitableEmployees($totalEstimatedTime)
    {
        $employees = User::where('is_active', 1)->where('employee_properties->department', 'Producción')->get();
        
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
