<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
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

        $spare_part = SparePart::create($request->all());
        $spare_part->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        event(new RecordCreated($spare_part));

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    
    public function show(SparePart $spare_part)
    {
        //
    }

    
    public function edit(SparePart $spare_part)
    {
        $media = $spare_part->getMedia()->all();

        return inertia('SparePart/Edit', compact('spare_part', 'media'));
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
        
        // update image
        $spare_part->clearMediaCollection();
        $spare_part->addMediaFromRequest('media')->toMediaCollection();
        $spare_part->save();

        event(new RecordEdited($spare_part));

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    public function updateWithMedia(Request $request, SparePart $spare_part)
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
          // update image
        $spare_part->clearMediaCollection();
        $spare_part->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        event(new RecordEdited($spare_part));

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);

    }

    
    public function destroy(SparePart $spare_part)
    {
        $spare_part->delete();

        event(new RecordDeleted($spare_part));
    }
}
