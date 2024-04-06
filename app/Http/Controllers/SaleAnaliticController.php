<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use App\Models\CatalogProductCompanySale;
use App\Models\CompanyBranch;
use App\Models\RawMaterial;
use App\Models\Sale;
use Carbon\Carbon;
use Demo\Product;

class SaleAnaliticController extends Controller
{

    public function index()
    {   
        $catalog_products = CatalogProduct::all(['id','name','part_number']);

        $current_month_sales = Sale::with(['companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct'])->where('is_sale_production', true)
            ->whereNotNull('authorized_user_name')
            ->whereMonth('created_at', today())
            ->get();

        $meet_ways = CompanyBranch::selectRaw('meet_way as concept, count(*) as total')
            ->groupBy('meet_way')
            ->get()
            ->toArray();

        return inertia('SaleAnalitic/Index', compact('current_month_sales', 'meet_ways', 'catalog_products'));
    }


    //Recupera el top 20 de productos mas vendidos, ya sea del catálogo o materia prima.
    //family: familia de produco (llaveros, emblemas, portaplacas, etc.), range:rango de dias, type:materia prima o prod. de catalogo
    public function fetchTopProducts($family, $range, $type)
    {
        $startDate = 0;

        // Determina la fecha de inicio según la opción seleccionada
        switch ($range) {
            case 'Mensual':
                $startDate = now()->subDays(30);
                break;
            case 'Bimestral':
                $startDate = now()->subDays(60);
                break;
            case 'Trimestral':
                $startDate = now()->subDays(90);
                break;
            case 'Quatrimestre':
                $startDate = now()->subDays(120);
                break;
            case 'Semestral':
                $startDate = now()->subDays(180);
                break;
        }

        if ($type == 'Producto de catálogo') { //si se hace la peticion para producto de catálogo

            // la familia la recupera como parámetro enviado desde la peticion axios en la vista index
            $products = CatalogProductCompanySale::with(['catalogProductCompany' => ['catalogProduct.media', 'company']])
            ->whereHas('catalogProductCompany.catalogProduct', function ($query) use ($family) {
                $query->where('part_number', 'like', $family . '%');
            })
            ->where('created_at', '>=', $startDate)
            ->get();
            
            $agrouped = $products->groupBy('catalogProductCompany.catalogProduct.part_number')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->catalogProductCompany?->catalogProduct?->name,
                    'part_number' => $group->first()->catalogProductCompany?->catalogProduct?->part_number,
                    'min_stock' => $group->first()->catalogProductCompany?->catalogProduct?->min_quantity,
                    'old_price' => $group->first()->catalogProductCompany?->old_price ?? 0,
                    'new_price' => $group->first()->catalogProductCompany?->new_price,
                    'quantity_sales' => $group->sum('quantity'),
                    'total_money' => $group->sum('quantity') * $group->first()->catalogProductCompany?->new_price,
                    'client' => $group->first()->catalogProductCompany?->company?->business_name,
                    'media' => $group->first()->catalogProductCompany?->catalogProduct?->media[0] ?? null,
                ];
            })
            ->sortByDesc('total_money')
            ->take(20); // Limitar a 20 elementos
            
            return response()->json(['items' => $agrouped]);

            //si se hace la peticion para materia prima
        } else {
            // la familia la recupera como parámetro enviado desde la peticion axios en la vista index
            $sales = CatalogProductCompanySale::with(['catalogProductCompany' => ['catalogProduct.rawMaterials', 'company']])
                ->whereHas('catalogProductCompany.catalogProduct', function ($query) use ($family){
                    $query->where('part_number', 'like', $family . '%');
                })
                ->where('created_at', '>=', $startDate)
                ->get();

                // Array para almacenar la cantidad total de cada materia prima utilizada
                $rawMaterialsTotals = [];

                // Iterar sobre cada venta para calcular la cantidad total de cada materia prima utilizada
                foreach ($sales as $sale) {
                    foreach ($sale->catalogProductCompany->catalogProduct->rawMaterials as $rawMaterial) {
                        $quantity = $sale->quantity * $rawMaterial->pivot->quantity; // Calcular la cantidad total utilizada en esta venta
                        $rawMaterialId = $rawMaterial->id;

                        // Agregar la cantidad total utilizada al array o sumarla si ya existe
                        if (isset($rawMaterialsTotals[$rawMaterialId])) {
                            $rawMaterialsTotals[$rawMaterialId] += $quantity;
                        } else {
                            $rawMaterialsTotals[$rawMaterialId] = $quantity;
                        }
                    }
                }

                // Crear un arreglo para almacenar los objetos con la información requerida
                $materialInfoArray = [];

                // Iterar sobre cada materia prima en $rawMaterialsTotals para obtener su información
                foreach ($rawMaterialsTotals as $rawMaterialId => $totalQuantity) {
                    $rawMaterial = RawMaterial::with('media')->find($rawMaterialId); // Suponiendo que tengas un modelo RawMaterial
                    
                    // Verificar si el número de parte coincide con $family
                    if (strpos($rawMaterial->part_number, substr($family, 2)) === 0) {
                        // Calcular el costo total
                        $totalCost = $totalQuantity * $rawMaterial->cost;

                        // Crear un objeto con la información deseada y agregarlo al arreglo
                        $materialInfoArray[] = [
                            'part_number' => $rawMaterial->part_number,
                            'name' => $rawMaterial->name,
                            'quantity_sales' => $totalQuantity,
                            'new_price' => $rawMaterial->cost,
                            'total_money' => $totalCost, //el nombre se le dio para que coincida con la tabla ya creada para prod. de catalo.
                            'media' => $rawMaterial->media[0] ?? null,
                        ];
                    }
                }

                // Función de comparación para ordenar de mayor a menor cantidad total utilizada
                usort($materialInfoArray, function ($a, $b) {
                    return $b['quantity_sales'] <=> $a['quantity_sales'];
                });

                // Limitar el arreglo a 20 elementos
                $materialInfoArray = array_slice($materialInfoArray, 0, 20);
                
                return response()->json(['items' => $materialInfoArray]);
            }
    }


    public function fetchCatalogProductSales($part_number, $range)
    {
        $startDate = 0;

        // Determina la fecha de inicio según la opción seleccionada
        switch ($range) {
            case 'Mensual':
                $startDate = now()->subDays(30);
                break;
            case 'Bimestral':
                $startDate = now()->subDays(60);
                break;
            case 'Trimestral':
                $startDate = now()->subDays(90);
                break;
            case 'Quatrimestre':
                $startDate = now()->subDays(120);
                break;
            case 'Semestral':
                $startDate = now()->subDays(180);
                break;
        }

         // la familia la recupera como parámetro enviado desde la peticion axios en la vista index
         $products = CatalogProductCompanySale::with(['catalogProductCompany' => ['catalogProduct.media', 'company']])
         ->whereHas('catalogProductCompany.catalogProduct', function ($query) use ($part_number) {
             $query->where('part_number', $part_number);
         })
         ->where('created_at', '>=', $startDate)
         ->get();
         
         $agrouped = $products->groupBy('catalogProductCompany.catalogProduct.part_number')
         ->map(function ($group) {
             return [
                 'name' => $group->first()->catalogProductCompany?->catalogProduct?->name,
                 'part_number' => $group->first()->catalogProductCompany?->catalogProduct?->part_number,
                 'min_stock' => $group->first()->catalogProductCompany?->catalogProduct?->min_quantity,
                 'old_price' => $group->first()->catalogProductCompany?->old_price ?? 0,
                 'new_price' => $group->first()->catalogProductCompany?->new_price,
                 'quantity_sales' => $group->sum('quantity'),
                 'total_money' => $group->sum('quantity') * $group->first()->catalogProductCompany?->new_price,
                 'client' => $group->first()->catalogProductCompany?->company?->business_name,
                 'media' => $group->first()->catalogProductCompany?->catalogProduct?->media[0] ?? null,
             ];
         })
         ->sortByDesc('total_money');

         $agroupedArray = $agrouped->toArray();
         
         return response()->json(['items' => $agrouped]);
    }


    //informacion de ventas cuando se selecciona un producto de catalogo
    public function fetchProductInfo($part_number)
    {
        // Obtener las ventas del producto
        $product = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')
            ->whereHas('catalogProductCompany.catalogProduct', function ($query) use ($part_number) {
                $query->where('part_number', $part_number);
            })
            ->get();

        // Verificar si hay al menos un elemento en el array
        if ($product->isNotEmpty()) {
            // Obtener el valor de new_price de la primera entrada del array
            $product_price = $product[0]->catalogProductCompany->new_price;
        } else {
            // Manejar el caso donde el array $product está vacío
            $product_price = 0;
        }

        // Agrupar ventas por fecha
        $grouped = $product->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
        });

        // Obtener arreglo con objetos formateados
        $sales = $grouped->map(function ($sales, $month) use ($product_price) {
            return [
                'month' => $month,
                'total_sales' => $sales->sum('quantity'),
                'money_sales' => $sales->sum('quantity') * $product_price,
            ];
        })->values()->sortBy('month');

        return response()->json(['items' => $sales, 'yearSales' => $this->getYearSales($product)]);
    }

    
    //informacion de ventas cuando se selecciona una materia prima
    public function fetchRawMaterialInfo($part_number)
    {
        // Obtener las ventas del producto
        $sales = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct.rawMaterials')
            ->whereHas('catalogProductCompany.catalogProduct.rawMaterials', function ($query) use ($part_number) {
                $query->where('part_number', $part_number);
            })
            ->get();

            
        // Verificar si hay al menos un elemento en el array
        if ($sales->isNotEmpty()) {
            // Obtener el valor de new_price de la primera entrada del array
            $product_price = $sales[0]->catalogProductCompany->catalogProduct->rawMaterials[0]->cost;
        } else {
            // Manejar el caso donde el array $sales está vacío
            $product_price = 0;
        }

        // Agrupar ventas por fecha
        $grouped = $sales->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
        });

        // Obtener arreglo con objetos formateados
        $sales_agrouped = $grouped->map(function ($sales, $month) use ($product_price) {
            return [
                'month' => $month,
                'total_sales' => $sales->sum('quantity'),
                'money_sales' => $sales->sum('quantity') * $product_price,
            ];
        })->values()->sortBy('month');

        return response()->json(['items' => $sales_agrouped, 'yearSales' => $this->getYearSales($sales)]);
    }

    public function getYearSales($productSales)
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        // $records = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')
        //     ->whereYear('created_at', '>=', $lastYear)
        //     ->get();

        $currentYearSales = $productSales->filter(function ($record) use ($currentYear) {
            return Carbon::parse($record->created_at)->year == $currentYear;
        })->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->month;
        })->map(function ($monthRecords) {
            return round($monthRecords->sum(function ($record) {
                $price = $record->catalogProductCompany->new_price ?? 0;
                return $record->quantity * $price;
            }), 1);
        })->toArray();

        $lastYearSales = $productSales->filter(function ($record) use ($lastYear) {
            return Carbon::parse($record->created_at)->year == $lastYear;
        })->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->month;
        })->map(function ($monthRecords) {
            return round($monthRecords->sum(function ($record) {
                $price = $record->catalogProductCompany->new_price ?? 0;
                return $record->quantity * $price;
            }), 1);
        })->toArray();

        // Rellenar con ceros los meses que no tienen ventas
        for ($i = 1; $i <= 12; $i++) {
            $currentYearSales[$i] = $currentYearSales[$i] ?? 0;
            $lastYearSales[$i] = $lastYearSales[$i] ?? 0;
        }

        return compact('lastYearSales', 'currentYearSales');
    }
}
