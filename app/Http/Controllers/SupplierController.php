<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    
    public function index()
    {
        $suppliers = SupplierResource::collection(Supplier::latest()->get());

        return inertia('Supplier/Index', compact('suppliers'));
    }

    
    public function create()
    {
        return inertia('Supplier/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'post_code' => 'nullable|string|min:4|max:9',
            'phone' => 'required|string|min:10|max:13',
            'banks' => 'array|min:1',
            'contacts' => 'array|min:1',
        ]);

       $supplier = Supplier::create($request->except('contacts'));

       foreach ($request->contacts as $contact ) {
            $supplier->contacts()->create($contact);
       }

        return to_route('suppliers.index');
    }

    
    public function show(Supplier $supplier)
    {
        $suppliers = SupplierResource::collection(Supplier::all());

        return inertia('Supplier/Show', compact('supplier', 'suppliers'));
    }

    
    public function edit(Supplier $supplier)
    {
        // return $supplier;
        return inertia('Supplier/Edit', compact('supplier'));
    }

    
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'post_code' => 'nullable|string|min:4|max:9',
            'phone' => 'required|string|min:10|max:13',
            'banks' => 'array|min:1',
        ]);

       $supplier->update($request->all());

        return to_route('suppliers.index');
    }

    
    public function destroy(Supplier $supplier)
    {
        $supplier_name = $supplier->name;
        $supplier->delete();

        return response()->json(['message' => "Producto eliminado: $supplier_name"]);
    }

    public function massiveDelete(Request $request)
    {

        foreach ($request->suppliers as $supplier) {
            $supplier = Supplier::find($supplier['id']);
            $supplier?->delete();
        }

        return response()->json(['message' => 'proveedor(es) eliminado(s)']);
    }
}
