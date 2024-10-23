<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\CatalogProduct;
use App\Models\Sale;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = Sale::with([
            'user:id,name', 'companyBranch:id,name'])
            ->where('user_id', auth()->id())
            ->where('is_sale_production', true)
            ->where('status', 'Producción terminada')
            ->orWhere('status', 'Enviado')
            ->orWhere('status', 'Parcialmente enviado')
            ->latest()
            ->paginate(20, ['id', 'promise_date', 'is_sale_production', 'company_branch_id', 'created_at', 'sent_at', 'sent_by', 'shipping_type', 'status', 'partialities']);

        return inertia('Shipping/Index', compact('shippings'));
    }

    //recupera todos los registros de envios
    public function indexAll()
    {
        $shippings = Sale::with([
            'user:id,name', 'companyBranch:id,name'])
            ->where('is_sale_production', true)
            ->where('status', 'Producción terminada')
            ->orWhere('status', 'Enviado')
            ->orWhere('status', 'Parcialmente enviado')
            ->latest()
            ->paginate(20, ['id', 'promise_date', 'user_id', 'company_branch_id', 'created_at', 'sent_at', 'sent_by', 'shipping_type', 'status', 'partialities']);

        return inertia('Shipping/IndexAll', compact('shippings'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Sale $shipping)
    {
        // Cargar las relaciones necesarias directamente desde el modelo Sale
        $shipping = Sale::with([
            'contact:id,name,email,phone',
            'user:id,name',
            'companyBranch:id,name,address,company_id',
            'companyBranch.company:id,business_name',
            // 'productions',
            'catalogProductCompanySales.catalogProductCompany:id,catalog_product_id',
            'catalogProductCompanySales.catalogProductCompany.catalogProduct.media',
            'catalogProductCompanySales.catalogProductCompany.catalogProduct.shippingRates',
            'catalogProductCompanySales.catalogProductCompany.catalogProduct:id,name,part_number'
        ])
        ->find($shipping->id, ['id', 'promise_date', 'user_id', 'company_branch_id', 'created_at', 'is_high_priority', 'notes', 'contact_id', 'shipping_company', 'status', 'partialities']);

        $shippings = Sale::where('status', 'Producción terminada')
            ->orWhere('status', 'Enviado')
            ->orWhere('status', 'Parcialmente enviado')
            ->get(['id']);

        // return $shipping;
        return inertia('Shipping/Show', compact('shipping', 'shippings'));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->shippings as $shipping) {
            $shipping = Sale::find($shipping['id']);
            $shipping?->delete();

            event(new RecordDeleted($shipping));
        }

        return to_route('shippings.index');
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');
        
        // Realiza la búsqueda
        $shippings = Sale::with([
            'user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id,sale_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]])
        // ->whereNotNull('tracking_guide')
        ->where('id', 'like', "%{$query}%")
        ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
        })
        ->orwhereHas('companyBranch', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%");
            })
        ->latest()
        ->paginate(100);

        return response()->json(['items' => $shippings], 200);
    }

    public function updateStatus(Sale $shipping, Request $request)
    {
        $shipping->status = $request->status;

        if ( $request->status === 'Enviado' ) {
            // Actualizar sent_at de todas las partialities a la fecha de hoy
            $shipping->partialities = collect($shipping->partialities)->map(function($partiality) {
                $partiality['sent_at'] = now();
                $partiality['sent_by'] = auth()->user()->name;
                $partiality['status'] = 'Enviado';
                return $partiality;
            })->toArray();
        }

        $shipping->save();

        return response()->json(['status' => $shipping->status]);
    }

    public function updateSent(Sale $shipping, Request $request)
    {
        $partialityIndex = $request->partialityIndex;

        // Convertir las parcialidades a un arreglo para poder modificarlas
        $partialities = $shipping->partialities;

        // Actualizar solo la parcialidad cuyo índice coincide
        $partialities[$partialityIndex]['sent_at'] = now();
        $partialities[$partialityIndex]['sent_by'] = auth()->user()->name;
        $partialities[$partialityIndex]['status'] = 'Enviado';

        // Asignar el arreglo modificado de vuelta
        $shipping->partialities = $partialities;

        
        // Verificar si todas las parcialidades tienen fecha de envío
        if (collect($shipping->partialities)->every(fn($p) => !is_null($p['sent_at']))) {
            $shipping->status = 'Enviado';
            
        } else if (collect($shipping->partialities)->some(fn($p) => !is_null($p['sent_at']))) {
            $shipping->status = 'Parcialmente enviado';
        }

        $shipping->save();
        
        return response()->json(['partialities' => $shipping->partialities, 'status' => $shipping->status]);
    }

    public function fetchCatalogProductInfo(CatalogProduct $catalog_product)
    {
        $catalog_product->load(['media', 'shippingRates']);

        return response()->json(['item' => $catalog_product]);
    }

    public function costsReport(Request $request)
    {   
        $startDate = $request->reportPeriod[0];
        $endDate = Carbon::parse($request->reportPeriod[1])->endOfDay();

        //recupera las ventas registradas en el periodo selecionado y que sea unicamente ventas y no stock
        $sales = Sale::with('companyBranch:id,name')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('is_sale_production', true)
            ->get(['id', 'shipping_company', 'freight_cost', 'partialities', 'status', 'company_branch_id']); 

        // return $sales;
        return inertia('Shipping/CostsReport', compact('sales'));
    }
}
