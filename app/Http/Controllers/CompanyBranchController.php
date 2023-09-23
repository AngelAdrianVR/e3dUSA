<?php

namespace App\Http\Controllers;

use App\Models\CompanyBranch;
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

    // public function show()
    // {
    //     //
    // }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'post_code_branch' => 'required|string',
            'sat_method' => 'required|string',
            'sat_type' => 'required|string',
            'sat_way' => 'required|string',
        ]);

        CompanyBranch::create([
            'name' => $request->name,
            'address' => $request->address,
            'post_code' => $request->post_code_branch,
            'sat_method' => $request->sat_method,
            'sat_type' => $request->sat_type,
            'sat_way' => $request->sat_way
        ]);

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

}
