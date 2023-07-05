<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($selectedMachine)
    {
        return inertia('SparePart/Create',compact('selectedMachine'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'supplier' => 'nullable|string',
            'quantity' => 'required|numeric|min:1',
            'cost' => 'required|numeric|min:0',
            'location' => 'required',
            'description' => 'nullable',
            'machine_id' => 'required',
        ]); 

        SparePart::create($request->all());

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    
    public function show(SparePart $spare_part)
    {
        //
    }

    
    public function edit(SparePart $spare_part)
    {
        return inertia('SparePart/Edit', compact('spare_part'));
    }

    
    public function update(Request $request, SparePart $spare_part)
    {
        $request->validate([
            'name' => 'required|string',
            'supplier' => 'nullable|string',
            'quantity' => 'required|numeric|min:1',
            'cost' => 'required|numeric|min:0',
            'location' => 'required',
            'description' => 'nullable',
            'machine_id' => 'required',
        ]); 

        $spare_part->update($request->all());

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    
    public function destroy(SparePart $spare_part)
    {
        $spare_part->delete();
    }
}
