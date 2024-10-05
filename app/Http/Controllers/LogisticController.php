<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Logistic;
use Illuminate\Http\Request;

class LogisticController extends Controller
{
    public function index()
    {
        $logistics = Logistic::with([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at' 
                => ['user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]]])
                ->whereHas('sale', function ($q) {
                    $q->where('user_id', auth()->id());
                })
            ->paginate(20, ['id', 'type', 'status', 'sent_at', 'sale_id']);

        return inertia('Logistic/Index', compact('logistics'));
    }

    //recupera todos los registros de logistica
    public function indexAll()
    {
        $logistics = Logistic::with([
            'sale:id,tracking_guide,promise_date,user_id,company_branch_id,created_at' 
                => ['user:id,name', 'companyBranch:id,name', 'productions' 
                => ['catalogProductCompanySale:id,catalog_product_company_id' 
                => ['catalogProductCompany:id,catalog_product_id' 
                => ['catalogProduct:id,name']]]]])
            ->paginate(20, ['id', 'type', 'status', 'sent_at', 'sale_id']);

        return inertia('Logistic/IndexAll', compact('logistics'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Logistic $logistic)
    {
        // Cargar las relaciones necesarias
        $logistic->load([
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
        $logistics = Logistic::with([
            'sale:id',
        ])->get(['id', 'sale_id']);

        return $logistic;
        return inertia('Logistic/Show', compact('logistic', 'logistics'));
    }

    public function edit(Logistic $logistic)
    {
        //
    }

    public function update(Request $request, Logistic $logistic)
    {
        //
    }

    public function destroy(Logistic $logistic)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->logistics as $logistic) {
            $logistic = Logistic::find($logistic['id']);
            $logistic?->delete();

            event(new RecordDeleted($logistic));
        }

        return to_route('logistics.index');
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');
        
        // Realiza la búsqueda
        $logistics = Logistic::with([
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

        return response()->json(['items' => $logistics], 200);
    }

}
