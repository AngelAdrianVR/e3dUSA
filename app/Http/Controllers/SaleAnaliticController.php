<?php

namespace App\Http\Controllers;

use App\Models\CatalogProductCompanySale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleAnaliticController extends Controller
{
    
    public function index()
    {
        // la familia la recupera como parámetro enviado desde la peticion axios en la vista index Borrar porque no sirve para nada, es solo par 
        //ver que retorna
        // $product = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')
        //     ->whereHas('catalogProductCompany.catalogProduct', function ($query) {
        //         $query->where('part_number', 'C-LL-CLE-0247');
        //     })
        //     ->get();

        //     $agrouped = $product->map(function ($prod) {
        //         return [
        //             'new_price' => $prod->catalogProductCompany->new_price,
        //             'quantity_sales' => $prod->quantity,
        //             'created_at' => $prod->created_at,
        //         ];
        //     })
        //     ->sortByDesc('created_at');


        // Obtener las ventas del producto
        $product = CatalogProductCompanySale::with('catalogProductCompany.catalogProduct')
        ->whereHas('catalogProductCompany.catalogProduct', function ($query) {
            $query->where('part_number', 'C-LL-CLE-0247');
        })
        ->get();

        // Mapear y agrupar por mes
        $grouped = $product->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
        });

        // Calcular la suma de las cantidades de ventas por mes
        $monthlySales = $grouped->map(function ($sales, $month) {
        return [
            'month' => $month,
            'total_sales' => $sales->sum('quantity'),
        ];
        })->values()->sortBy('month');


            
            // return $monthlySales;


        return inertia('SaleAnalitic/Index');
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
                    'name' => $group->first()->catalogProductCompany->catalogProduct->name,
                    'part_number' => $group->first()->catalogProductCompany->catalogProduct->part_number,
                    'min_stock' => $group->first()->catalogProductCompany->catalogProduct->min_quantity,
                    'old_price' => $group->first()->catalogProductCompany->old_price,
                    'new_price' => $group->first()->catalogProductCompany->new_price,
                    'quantity_sales' => $group->sum('quantity'),
                    'total_money' => $group->sum('quantity') * $group->first()->catalogProductCompany->new_price,
                    'client' => $group->first()->catalogProductCompany->company->business_name,
                    'media' => $group->first()->catalogProductCompany->catalogProduct->media[0],
                ];
            })
            ->sortByDesc('quantity_sales')
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

        // Mapear y agrupar por mes
        $grouped = $product->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
        });

        // Calcular la suma de las cantidades de ventas por mes
        $monthlySales = $grouped->map(function ($sales, $month) {
        return [
            'month' => $month,
            'total_sales' => $sales->sum('quantity'),
        ];
        })->values()->sortBy('month');

        return response()->json(['items' => $monthlySales]);
    }


}
