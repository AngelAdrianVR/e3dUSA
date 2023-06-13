<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use Illuminate\Http\Request;

class CatalogProductController extends Controller
{
    
    public function index()
    {
        return inertia('CatalogProduct/Index');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(CatalogProduct $catalogProduct)
    {
        //
    }

    
    public function edit(CatalogProduct $catalogProduct)
    {
        //
    }

    
    public function update(Request $request, CatalogProduct $catalogProduct)
    {
        //
    }

    
    public function destroy(CatalogProduct $catalogProduct)
    {
        //
    }
}
