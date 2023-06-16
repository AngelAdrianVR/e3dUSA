<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function index()
    {
        $companies = Company::all();

        return inertia('Company/Index', compact('companies'));
    }

    
    public function create()
    {
        return inertia('Company/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|unique:companies,business_name',
            'phone' => 'required|min:10|max:12',
            'rfc' => 'required|string|unique:companies,rfc',
            'post_code' => 'required',
            'fiscal_address' => 'required',
        ]);

        Company::create($request->all());

        return to_route('companies.index');
    }

    
    public function show(Company $company)
    {
        //
    }

    
    public function edit(Company $company)
    {
        return inertia('Company/Edit', compact('company'));
    }

    
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'business_name' => 'required|string|unique:companies,business_name',
            'phone' => 'required|min:10|max:13',
            'rfc' => 'required|string|unique:companies,rfc',
            'post_code' => 'required',
            'fiscal_address' => 'required',
        ]);

        $company->update($request->all());

        return to_route('companies.index');
    }

    
    public function destroy(Company $company)
    {
        //
    }
}
