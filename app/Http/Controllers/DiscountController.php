<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    
    public function index()
    {
        $discounts = DiscountResource::collection(Discount::latest()->get());

        return inertia('Discount/Index', compact('discounts'));
    }

    
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable',
            'amount' => 'required|numeric|min:0',

        ]);

        $discount = Discount::create($request->all());

        event(new RecordCreated($discount));

        return to_route('discounts.index');
    }

    
    public function show(Discount $discount)
    {
        //
    }

    
    public function edit(Discount $discount)
    {
        //
    }

    
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable',
            'amount' => 'required|numeric|min:0',

        ]);

        $discount->update($request->all());

        event(new RecordEdited($discount));

        return to_route('discounts.index');
    }

    
    public function destroy(Discount $discount)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->discounts as $discount) {
            $discount = Discount::find($discount['id']);
            $discount?->delete();
            
            event(new RecordDeleted($discount));
        }

        return response()->json(['message' => 'Descuento(s) eliminado(s)']);
    }
}
