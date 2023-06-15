<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    
    public function index()
    {

        return inertia('Purchase/Index');
    }

    
    public function create()
    {
        $suppliers = Supplier::all();
        return inertia('Purchase/Create', compact('suppliers'));
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Purchase $purchase)
    {
        //
    }

    
    public function edit(Purchase $purchase)
    {
        //
    }

    
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    
    public function destroy(Purchase $purchase)
    {
        //
    }
}
