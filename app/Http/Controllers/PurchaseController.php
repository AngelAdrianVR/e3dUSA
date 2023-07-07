<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
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
        
        return inertia('Purchase/Index', compact('purchases'));
    }

    
    public function create()
    {
        $suppliers = Supplier::with('contacts')->get();
        $raw_materials = RawMaterial::all();


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
        $purchases = PurchaseResource::collection(Purchase::with('user','supplier')->get());
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
        //
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
        $company = Company::find($request->company_id);

        $clone = $company->replicate()->fill([
            'business_name' => $company->business_name . ' (Copia)',
        ]);

        $clone->save();

        foreach ($company->companyBranches as $branch) {
            $branch = CompanyBranch::find($branch->id);
            $branch_clone = $branch->replicate()->fill([
                'company_id' => $clone->id
            ]);

            $branch_clone->save();

            foreach ($branch->contacts as $contact) {
                $branch_clone->contacts()->create($contact->toArray());
            }

        }

        foreach ($company->catalogProducts as $product) {
            $pivot = [
                'old_date' => $product->pivot->old_date,
                'new_date' => $product->pivot->new_date,
                'old_price' => $product->pivot->old_price,
                'new_price' => $product->pivot->new_price,
                'old_currency' => $product->pivot->old_currency,
                'new_currency' => $product->pivot->new_currency,
            ];

            $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
        }

        return response()->json(['message' => "Cliente clonado: {$clone->business_name}", 'newItem' => CompanyResource::make($clone)]);
    }
}
