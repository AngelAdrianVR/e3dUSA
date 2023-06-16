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

    
    public function show(CompanyBranch $companyBranch)
    {
        //
    }

    
    public function edit(CompanyBranch $companyBranch)
    {
        //
    }

    
    public function update(Request $request, CompanyBranch $companyBranch)
    {
        //
    }

    
    public function destroy(CompanyBranch $companyBranch)
    {
        //
    }
}
