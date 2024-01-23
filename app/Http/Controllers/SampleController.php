<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Models\Sample;
use App\Http\Resources\SampleResource;
use App\Models\CatalogProduct;
use App\Models\CompanyBranch;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    
    public function index()
    {
        // $samples = SampleResource::collection(Sample::with(['catalogProduct:id,name', 'companyBranch:id,name', 'user:id,name'])->latest()
        // ->get(['id', 'name', 'quantity', 'sent_at', 'returned_at', 'comments', 'catalog_product_id', 'company_branch_id', 'user_id']));

        $pre_samples = Sample::with(['catalogProduct:id,name', 'companyBranch:id,name', 'user:id,name'])->latest()
        ->get(['id', 'name', 'quantity', 'sent_at', 'returned_at', 'comments', 'catalog_product_id', 'company_branch_id', 'user_id', 'sale_order_at', 'will_back']);
        $samples = $pre_samples->map(function ($sample) {

            $status = ['label' => 'Enviado. Esperando respuesta',
                    'text-color' => 'text-amber-500',
                    'border-color' => 'border-amber-500',        
                    'progress' => '1/3'        
                    ];

        if($sample->returned_at) {

            $status = ['label' => 'Muestra devuelta',
                    'text-color' => 'text-blue-500',
                    'border-color' => 'border-blue-500',        
                    'progress' => '2/3'        
                    ];

                    if($sample->sale_order_at){
                        $status = ['label' => 'Orden generada. Venta exitosa',
                    'text-color' => 'text-green-600',
                    'border-color' => 'border-green-600',        
                    'progress' => '3/3'        
                    ];
                    }
        }           

            return [
                'id' => $sample->id,
                'folio' => 'MUE-' . str_pad($sample->id, 4, "0", STR_PAD_LEFT),
                'name' => $sample->name,
                'will_back' => $sample->will_back,
                'sale_order_at' => $sample->sale_order_at?->isoFormat('DD MMM YYYY'),
                'quantity' => $sample->quantity,
                'sent_at' => $sample->sent_at?->isoFormat('DD MMM YYYY'),
                'returned_at' => $sample->returned_at?->isoFormat('DD MMM YYYY'),
                'comments' => $sample->comments,
                'catalog_product' => $sample->catalogProduct,
                'company_branch' => $sample->companyBranch,
                'user' => $sample->user,
                'status' => $status,
            ];
        });

        // return $samples;
        return inertia('Sample/Index', compact('samples'));
    }

    
    public function create()
    {
        $catalog_products = CatalogProduct::latest()->get(['id', 'name']);
        $pre_company_branches = CompanyBranch::with(['contacts'])->latest()->get(['id', 'name']);
        $company_branches = $pre_company_branches->map(function ($company_branch){

            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'contacts' => $contacts,
            ];
        });

        // return $company_branches;
        return inertia('Sample/Create', compact('catalog_products', 'company_branches'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'will_back' => 'boolean',
            'devolution_date' => $request->will_back ? 'required|date' : 'nullable|date',
            'sent_at' => 'required|date|before:tomorrow',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample = Sample::create($request->except('media') + [
            'user_id' => auth()->id()
        ]);

        $sample->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        $can_authorize = auth()->user()->can('Autorizar muestra') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $sample->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        }

        event(new RecordCreated($sample));

        return to_route('samples.index');
    }

    
    public function show($sample_id)
    {   
        $sample = SampleResource::make(Sample::with(['user:id,name', 'companyBranch', 'catalogProduct.media'])->latest()->find($sample_id));
        $samples =Sample::latest()->get(['id', 'name']);

        // return $sample;

        return inertia('Sample/Show', compact('sample', 'samples'));
    }

    
    public function edit(Sample $sample)
    {
        $catalog_products = CatalogProduct::latest()->get(['id', 'name']);
        $pre_company_branches = CompanyBranch::with(['contacts'])->latest()->get(['id', 'name']);
        $company_branches = $pre_company_branches->map(function ($company_branch){

            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'contacts' => $contacts,
            ];
        });

        return inertia('Sample/Edit', compact('sample', 'catalog_products', 'company_branches'));
    }

    
    public function update(Request $request, Sample $sample)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'will_back' => 'boolean',
            'devolution_date' => $request->will_back ? 'required|date' : 'nullable|date',
            'sent_at' => 'required|date|before:tomorrow',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample->update($request->except('media'));

        event(new RecordEdited($sample));

        return to_route('samples.index');
    }

    public function updateWithMedia(Request $request, Sample $sample)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'sent_at' => 'required|date|before:tomorrow',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample->update($request->all());
          // update image
        $sample->clearMediaCollection();
        $sample->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        event(new RecordEdited($sample));


        return to_route('samples.index');

    }

    
    public function destroy(Sample $sample)
    {
        $sample->delete();

        event(new RecordDeleted($sample));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->samples as $sample) {
            $sample = Sample::find($sample['id']);
            $sample?->delete();

            event(new RecordDeleted($sample));
        }

        return response()->json(['message' => 'reunion(es) eliminada(s)']);
    }

    public function returned(Request $request, Sample $sample)
    {
        $request->validate([
            'comments' => 'required|string'
        ]);

        $sample->update([
            'comments' => $request->comments,
            'returned_at' => now(),
        ]);

        return to_route('samples.index');
    }

    public function saleOrder(Sample $sample)
    {

        $sample->update([
            'sale_order_at' => now(),
        ]);

    }
}
