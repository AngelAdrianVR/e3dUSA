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
        $samples = SampleResource::collection(Sample::with('catalogProduct', 'companyBranch', 'user')->latest()->get());
        // return $samples;
        return inertia('Sample/Index', compact('samples'));
    }

    
    public function create()
    {
        $catalog_products = CatalogProduct::latest()->get();
        $company_branches = CompanyBranch::with('contacts')->latest()->get();

        return inertia('Sample/Create', compact('catalog_products', 'company_branches'));
    }

    
    public function store(Request $request)
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

    
    public function show(Sample $sample)
    {   
        $samples = SampleResource::collection(Sample::with('user', 'companyBranch', 'catalogProduct.media')->latest()->get());
        // return $samples;
        return inertia('Sample/Show', compact('sample', 'samples'));
    }

    
    public function edit(Sample $sample)
    {
        $catalog_products = CatalogProduct::latest()->get();
        $company_branches = CompanyBranch::latest()->get();

        return inertia('Sample/Edit', compact('sample', 'catalog_products', 'company_branches'));
    }

    
    public function update(Request $request, Sample $sample)
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

        $sample->update($request->except('media') + [
            'user_id' => auth()->id()
        ]);

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
