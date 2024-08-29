<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Models\CatalogProductCompany;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Design;
use Illuminate\Http\Request;

class CompanyBranchController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'post_code_branch' => 'required|string',
            'sat_method' => 'required|string',
            'sat_type' => 'required|string',
            'sat_way' => 'required|string',
        ]);

       $company_branch = CompanyBranch::create([
            'name' => $request->name,
            'address' => $request->address,
            'state' => $request->state,
            'post_code' => $request->post_code_branch,
            'sat_method' => $request->sat_method,
            'sat_type' => $request->sat_type,
            'sat_way' => $request->sat_way
        ]);

        event(new RecordCreated($company_branch));

    }

    public function clearImportantNotes(CompanyBranch $company_branch)
    {
        $company_branch->important_notes = null;
        $company_branch->save();

        return response()->json(['message' => 'Notas borradas']);
    }

    public function storeImportantNotes(CompanyBranch $company_branch, Request $request)
    {
        $company_branch->important_notes = $request->notes;
        $company_branch->save();

        return response()->json(['message' => 'Notas guardadas']);
    }

    public function updateProductPrice(CatalogProductCompany $product_company, Request $request)
    {
        $product_company->update([
            'oldest_date' => $product_company->old_date,
            'oldest_price' => $product_company->old_price,
            'oldest_currency' => $product_company->old_currency,
            'old_date' => $product_company->new_date,
            'old_price' => $product_company->new_price,
            'old_currency' => $product_company->new_currency,
            'new_date' => now(),
            'new_price' => $request->new_price,
            'new_currency' => $request->new_currency,
            'user_id' => auth()->id()
        ]);
    }

    public function fetchDesignInfo(CompanyBranch $company_branch)
    {   
        //recupero todos los diseños de esa sucursal
        $company_branch_designs = Design::with(['user:id,name', 'designType:id,name'])->where('company_branch_name', $company_branch->name)
            ->get(['id', 'name', 'created_at', 'design_type_id', 'finished_at', 'started_at', 'user_id']);
        
        $company_products = CatalogProductCompany::with('catalogProduct:id,name,part_number')->where('company_id', $company_branch->company_id)->get();

        return response()->json(compact('company_branch_designs', 'company_products'));
    }

}
