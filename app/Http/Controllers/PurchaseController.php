<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Http\Resources\RawMaterialResource;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    
    public function index()
    {
        $purchases = PurchaseResource::collection(Purchase::with('contact', 'supplier', 'user')->latest()->get());
        // return $purchases;        
        return inertia('Purchase/Index', compact('purchases'));
    }

    
    public function create()
    {
        $suppliers = Supplier::with('contacts')->get();
        $raw_materials = RawMaterialResource::collection(RawMaterial::all());

        // return $raw_materials;


        return inertia('Purchase/Create', compact('suppliers', 'raw_materials'));
    }

    
    public function store(Request $request)
    {
        $validation = $request->validate([
        'notes' => 'nullable',
        'expected_delivery_date' => 'nullable|date|after:yesterday',
        // 'is_iva_included' => 'nullable|boolean',
        'supplier_id' => 'required',
        'contact_id' => 'required',
        'products' => 'nullable|min:1',
        'bank_information' => 'required',
        ]);

        Purchase::create($validation + ['user_id' => auth()->user()->id]);

        return to_route('purchases.index');
    }

    
    public function show(Purchase $purchase)
    {
        $purchases = PurchaseResource::collection(Purchase::with('user','supplier')->latest()->get());
        // return $purchases;
        return inertia('Purchase/Show', compact('purchase', 'purchases'));
    }

    
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::with('contacts')->get();
        $raw_materials = RawMaterial::all();
        return inertia('Purchase/Edit', compact('purchase', 'suppliers', 'raw_materials'));
    }

    
    public function update(Request $request, Purchase $purchase)
    {
        $validation = $request->validate([
            'notes' => 'nullable',
            'expected_delivery_date' => 'nullable|date|after:yesterday',
            // 'is_iva_included' => 'nullable|boolean',
            'supplier_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|min:1',
            'bank_information' => 'required',
            ]);
    
            $purchase->update($validation + ['user_id' => auth()->user()->id]);
    
            return to_route('purchases.index');
    }

    
    public function destroy(Purchase $purchase)
    {
        //
    }

    public function markOrderDone(Purchase $currentPurchase)
    {
        $currentPurchase->emited_at = now();
        $currentPurchase->status = 2; //2. Emitido
        $currentPurchase->save();

        return to_route('purchases.index');
    }

    public function markOrderRecieved(Purchase $currentPurchase)
    {
        $currentPurchase->recieved_at = now();
        $currentPurchase->status = 3; //3. Rebibido
        $currentPurchase->save();

        return to_route('purchases.index');
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->purchases as $purchase) {
            $purchase = Purchase::find($purchase['id']);
            $purchase?->delete();
        }

        return response()->json(['message' => 'Órden(es) eliminada(s)']);
    }

    public function clone(Request $request)
    {
        $purchase = Purchase::find($request->purchase_id);

        $clone = $purchase->replicate()->fill([
            'folio' => $purchase->folio . ' (Copia)',
        ]);

        $clone->save();

    

        return response()->json(['message' => "Órden de compra clonada: {$clone->creator}", 'newItem' => PurchaseResource::make($clone)]);
    }
}
