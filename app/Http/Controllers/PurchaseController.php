<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\RawMaterial;
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
        $raw_materials = RawMaterial::all();

        return inertia('Purchase/Create', compact('suppliers', 'raw_materials'));
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
