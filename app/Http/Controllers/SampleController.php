<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Http\Resources\SampleResource;
use App\Models\CatalogProduct;
use App\Models\CompanyBranch;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    
    public function index()
    {
        $samples = SampleResource::collection(Sample::with('catalogProduct', 'companyBranch')->latest()->get());
        // return $samples;
        return inertia('Sample/Index', compact('samples'));
    }

    
    public function create()
    {
        $catalog_products = CatalogProduct::latest()->get();
        $company_branches = CompanyBranch::with('contacts')->latest()->get();

        // return $company_branches;

        return inertia('Sample/Create', compact('catalog_products', 'company_branches'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'sent_at' => 'required|date|before:tomorrow',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'required',
            'company_branch_id' => 'required',
            'contact_id' => 'required'
        ]);

        Sample::create($request->all() + [
            'user_id' => auth()->id()
        ]);

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
            'catalog_product_id' => 'required',
            'company_branch_id' => 'required',
            'contact_id' => 'required'
        ]);

        $sample->update($request->all() + [
            'user_id' => auth()->id()
        ]);

        return to_route('samples.index');
    }

    
    public function destroy(Sample $sample)
    {
        //
    }
}
