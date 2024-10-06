<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Sale;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = Sale::with([
            'user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id,sale_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]])
            ->where('user_id', auth()->id())
            ->whereNotNull('tracking_guide')
            ->latest()
            ->paginate(20, ['id', 'tracking_guide', 'promise_date', 'user_id', 'company_branch_id', 'created_at', 'sent_at', 'sent_by', 'shipping_type', 'status']);

        // return $shippings;
        return inertia('Shipping/Index', compact('shippings'));
    }

    //recupera todos los registros de envios
    public function indexAll()
    {
        $shippings = Sale::with([
            'user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id,sale_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]])
            ->whereNotNull('tracking_guide')
            ->latest()
            ->paginate(20, ['id', 'tracking_guide', 'promise_date', 'user_id', 'company_branch_id', 'created_at', 'sent_at', 'sent_by', 'shipping_type', 'status']);

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
        $shipping = Sale::whereNotNull('tracking_guide')
        ->with([
            'contact:id,name,email,phone',
            'user:id,name',
            'companyBranch:id,name,address,company_id',
            'companyBranch.company:id,business_name',
            'productions',
            'catalogProductCompanySales.catalogProductCompany:id,catalog_product_id',
            'catalogProductCompanySales.catalogProductCompany.catalogProduct.media',
            'catalogProductCompanySales.catalogProductCompany.catalogProduct:id,name,part_number'
        ])
        ->find($shipping->id, ['id', 'tracking_guide', 'promise_date', 'user_id', 'company_branch_id', 'created_at', 'is_high_priority', 'notes', 'contact_id', 'shipping_company']);

        $shippings = Sale::whereNotNull('tracking_guide')
            ->get(['id']);

        // return $shipping;
        return inertia('Shipping/Show', compact('shipping', 'shippings'));
    }

    public function edit(Shipping $shipping)
    {
        //
    }

    public function update(Request $request, Shipping $shipping)
    {
        //
    }

    public function destroy(Shipping $shipping)
    {
        //
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
        ->whereNotNull('tracking_guide')
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
        $shipping->save();

        return response()->json(['status' => $shipping->status]);
    }
}
