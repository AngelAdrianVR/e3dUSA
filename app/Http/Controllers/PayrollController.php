<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    
    public function index()
    {
        $payrolls = PayrollResource::collection(Payroll::latest()->get());

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

    
    public function show($payroll_id)
    {
        $payroll = PayrollResource::make(Payroll::find($payroll_id));
        $users = User::all();
        return inertia('Payroll/Show', compact('payroll', 'users'));
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
