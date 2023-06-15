<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function index()
    {
        return inertia('Company/Index');
    }

    
    public function create()
    {
        return inertia('Company/Create');
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Company $company)
    {
        //
    }

    
    public function edit(Company $company)
    {
        //
    }

    
    public function update(Request $request, Company $company)
    {
        //
    }

    
    public function destroy(Company $company)
    {
        //
    }
}
