<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    
    public function index()
    {
        return inertia('Sale/Index');
    }

    
    public function create()
    {
        return inertia('Sale/Create');
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Sale $sale)
    {
        //
    }

    
    public function edit(Sale $sale)
    {
        //
    }

    
    public function update(Request $request, Sale $sale)
    {
        //
    }

    
    public function destroy(Sale $sale)
    {
        //
    }
}
