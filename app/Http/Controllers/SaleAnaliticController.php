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
        $monthlySales = $grouped->map(function ($sales, $month) use ($product_price) {
            return [
                'month' => $month,
                'total_sales' => $sales->sum('quantity'),
                'money_sales' => $sales->sum('quantity') * $product_price,
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
        ->whereHas('catalogProductCompany.catalogProduct', function ($query) {
            $query->where('part_number', 'C-LL-CLE-0247');
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
        $monthlySales = $grouped->map(function ($sales, $month) use ($product_price) {
            return [
                'month' => $month,
                'total_sales' => $sales->sum('quantity'),
                'money_sales' => $sales->sum('quantity') * $product_price,
            ];
        })->values()->sortBy('month');

        return response()->json(['items' => $monthlySales]);
    }


}
