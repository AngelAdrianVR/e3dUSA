<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineResource;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    
    public function index()
    {
        $machines = MachineResource::collection(Machine::latest()->get());

        return inertia('Machine/Index', compact('machines'));
    }

    
    public function create()
    {
        return inertia('Machine/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'serial_number' => 'nullable',
            'wight' => 'nullable|numeric|min:1',
            'width' => 'nullable|numeric|min:1',
            'large' => 'nullable|numeric|min:1',
            'height' => 'nullable|numeric|min:1',
            'cost' => 'nullable|numeric|min:1',
            'supplier' => 'nullable|string',
            'aquisition_date' => 'nullable|date|before:tomorrow',
            'days_next_maintenance' => 'required|numeric|min:7|max:60',
        ]);

        Machine::create($request->all());

        return to_route('machines.index');
    }

    
    public function show(Machine $machine)
    {
        $machines = MachineResource::collection(Machine::with('maintenances', 'spareParts')->get());

        return inertia('Machine/Show', compact('machine', 'machines'));
        
    }

    
    public function edit(Machine $machine)
    {
        return inertia('Machine/Edit', compact('machine'));
    }

    
    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'name' => 'required',
            'serial_number' => 'nullable',
            'wight' => 'nullable|numeric|min:1',
            'width' => 'nullable|numeric|min:1',
            'large' => 'nullable|numeric|min:1',
            'height' => 'nullable|numeric|min:1',
            'cost' => 'nullable|numeric|min:1',
            'supplier' => 'nullable|string',
            'aquisition_date' => 'nullable|date|before:tomorrow',
            'days_next_maintenance' => 'required|numeric|min:7|max:60',
        ]);

        $machine->update($request->all());

        return to_route('machines.index');
    }

    
    public function destroy(Machine $machine)
    {
        //
    }
}
