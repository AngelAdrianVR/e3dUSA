<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    
    public function index()
    {
        $payrolls = Payroll::latest()->get();

        return inertia('Payroll/Index', compact('payrolls'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Payroll $payroll)
    {
        //
    }

   
    public function edit(Payroll $payroll)
    {
        //
    }

    
    public function update(Request $request, Payroll $payroll)
    {
        //
    }

    
    public function destroy(Payroll $payroll)
    {
        //
    }
}
