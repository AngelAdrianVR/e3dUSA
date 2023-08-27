<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
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
            'days_next_maintenance' => 'required|numeric|min:7',
        ]);

        $machine = Machine::create($request->all());
        $machine->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('images'));

        event(new RecordCreated($machine));

        return to_route('machines.index');
    }

    
    public function show(Machine $machine)
    {
        $machines = MachineResource::collection(Machine::with('maintenances', 'spareParts', 'media')->get());

        // return $machines;
        return inertia('Machine/Show', compact('machine', 'machines'));
        
    }

    public function edit(Machine $machine)
    {
        $media = $machine->getFirstMedia();

        return inertia('Machine/Edit', compact('machine', 'media'));
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
            'days_next_maintenance' => 'required|numeric|min:7',
        ]);

        $machine->update($request->all());
        
        event(new RecordEdited($machine));

        return to_route('machines.index');
    }

    public function updateWithMedia(Request $request, Machine $machine)
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
            'days_next_maintenance' => 'required|numeric|min:7',
        ]);

        $machine->update($request->all());
          // update image
        $machine->clearMediaCollection('images');
        $machine->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('images'));

        event(new RecordEdited($machine));

        return to_route('machines.index');

    }

    
    public function destroy(Machine $machine)
    {
        $machine->delete();

        event(new RecordDeleted($machine));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->machines as $machine) {
            $machine = Machine::find($machine['id']);
            $machine?->delete();

            event(new RecordDeleted($machine));
        }

        return response()->json(['message' => 'Maquina(s) eliminada(s)']);
    }

    public function uploadFiles(Request $request, Machine $machine)
    {
        $request->validate([
            'media' => 'required'
        ]);


        $machine->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('files'));

       return to_route('machines.show', ['machine'=> $machine]);
    }

    public function QRSearchMachine(Request $request)
    {

        $request->validate([ 
            'barCode' => 'required|string'
        ]);

        $serial_number = $request->barCode;


        $machine = MachineResource::make(Machine::with('maintenances', 'spareParts', 'media')->where('serial_number', $serial_number)->first());

        // dd($machine);

        return response()->json(['item' => $machine]);
    }
}
