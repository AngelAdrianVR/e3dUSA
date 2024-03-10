<?php

namespace App\Http\Controllers;

use App\Models\CatalogProductCompanySale;
use App\Models\CompanyBranch;
use App\Models\Sale;
use Carbon\Carbon;

class SaleAnaliticController extends Controller
{

    public function index()
    {
        $current_month_sales = Sale::with(['companyBranch', 'catalogProductCompanySales.catalogProductCompany.catalogProduct'])->where('is_sale_production', true)
            ->whereNotNull('authorized_user_name')
            ->whereMonth('created_at', today())
            ->get();

        $meet_ways = CompanyBranch::selectRaw('meet_way as concept, count(*) as total')
            ->groupBy('meet_way')
            ->get()
            ->toArray();
        // return $meet_ways;

        return inertia('SaleAnalitic/Index', compact('current_month_sales', 'meet_ways'));
    }


    public function fetchTopProducts($family, $range)
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
        }

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
    }


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

        // Resto del código...
        $grouped = $product->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
        });

        // Resto del código...
        $sales = $grouped->map(function ($sales, $month) use ($product_price) {
            return [
                'month' => $month,
                'total_sales' => $sales->sum('quantity'),
                'money_sales' => $sales->sum('quantity') * $product_price,
            ];
        })->values()->sortBy('month');

        return response()->json(['items' => $sales, 'yearSales' => $this->getYearSales($product)]);
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
