<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = Shipping::with([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at' 
                => ['user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]]])
                ->whereHas('sale', function ($q) {
                    $q->where('user_id', auth()->id());
                })
            ->paginate(20, ['id', 'type', 'status', 'sent_at', 'sale_id']);

        return inertia('Shipping/Index', compact('shippings'));
    }

    //recupera todos los registros de envios
    public function indexAll()
    {
        $shippings = Shipping::with([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at' 
                => ['user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]]])
            ->paginate(20, ['id', 'type', 'status', 'sent_at', 'sale_id']);

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

    public function show(Shipping $shipping)
    {
        // Cargar las relaciones necesarias
        $shipping->load([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at,is_high_priority,notes,contact_id,shipping_company',
            'sale.contact:id,name,email,phone',
            'sale.user:id,name',
            'sale.companyBranch.company:id,business_name',
            'sale.productions',
            'sale.productions.catalogProductCompanySale',
            'sale.productions.catalogProductCompanySale.catalogProductCompany:id,catalog_product_id',
            'sale.productions.catalogProductCompanySale.catalogProductCompany.catalogProduct.media',
            'sale.productions.catalogProductCompanySale.catalogProductCompany.catalogProduct:id,name'
        ]);
        $shippings = Shipping::with([
            'sale:id',
        ])->get(['id', 'sale_id']);

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
            $shipping = Shipping::find($shipping['id']);
            $shipping?->delete();

            event(new RecordDeleted($shipping));
        }

        return to_route('shippings.index');
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');
        
        // Realiza la búsqueda
        $shippings = Shipping::with([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at' 
                => ['user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]]])
        ->where('id', 'like', "%{$query}%")
        ->orWhereHas('sale', function ($q) use ($query) {
            $q->whereHas('user', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%");
            });
            $q->orwhereHas('companyBranch', function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%");
            });
            $q->orWhere('id', 'like', "%{$query}%"); // Aplicar el filtro de 'id' sobre la tabla 'sale'
        })  
        ->latest()
        ->paginate(100);

        return response()->json(['items' => $shippings], 200);
    }
}
