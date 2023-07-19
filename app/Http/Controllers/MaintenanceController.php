<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($selectedMachine)
    {
        return inertia('Maintenance/Create', compact('selectedMachine'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'maintenance_type' => 'required',
            'problems' => $request->maintenance_type =='Correctivo' ? 'required' : 'nullable' . '|string',
            'actions' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'responsible' => 'required|string',
            'machine_id' => 'required|numeric',
        ]);

        $maintenance = Maintenance::create($request->except('maintenance_type_id') + [
            'maintenance_type_id' => $request->maintenance_type == 'Preventivo' ? '0' : '1',
        ]);

        $maintenance->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());
        
        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    
    public function show(Maintenance $maintenance)
    {
        //
    }

    
    public function edit(Maintenance $maintenance)
    {
        $media = $maintenance->getMedia()->all();

        return inertia('Maintenance/Edit', compact('maintenance', 'media'));
    }

    
    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'maintenance_type' => 'required',
            'problems' => $request->maintenance_type =='Correctivo' ? 'required' : 'nullable' . '|string',
            'actions' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'responsible' => 'required|string',
            'machine_id' => 'required|numeric',
        ]);

        $maintenance->update($request->except('maintenance_type_id') + [
            'maintenance_type_id' => $request->maintenance_type == 'Preventivo' ? '0' : '1',
        ]);

         // update image
         $maintenance->clearMediaCollection();
         $maintenance->addMediaFromRequest('media')->toMediaCollection();
         $maintenance->save();

        return redirect()->route('machines.show', ['machine'=> $request->machine_id]);
    }

    
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

    }
}
