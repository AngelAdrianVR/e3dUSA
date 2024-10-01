<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
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
        $machine = Machine::find($selectedMachine);

        return inertia('Maintenance/Create', compact('machine'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maintenance_type' => 'required',
            'problems' => $request->maintenance_type == 'Correctivo' ? 'required' : 'nullable' . '|string',
            'actions' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'responsible' => 'required|string',
            'machine_id' => 'required|numeric',
            'start_date' => 'required|date',
        ]);

        if ($request->maintenance_type == 'Preventivo') {
            $maintenance_type_id = 1;
        } else if ($request->maintenance_type == 'Correctivo') {
            $maintenance_type_id = 2;
        } else if ($request->maintenance_type == 'Limpieza') {
            $maintenance_type_id = 3;
        }

        $maintenance = Maintenance::create($request->all() + compact('maintenance_type_id'));

        $maintenance->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());

        event(new RecordCreated($maintenance));

        return redirect()->route('machines.show', ['machine' => $request->machine_id]);
    }


    public function show(Maintenance $maintenance)
    {
        //
    }


    public function edit(Maintenance $maintenance)
    {
        $media = $maintenance->getMedia()->all();
        $maintenance->load('machine');

        return inertia('Maintenance/Edit', compact('maintenance', 'media'));
    }


    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'maintenance_type' => 'required',
            'problems' => $request->maintenance_type == 'Correctivo' ? 'required' : 'nullable' . '|string',
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

        event(new RecordEdited($maintenance));

        return redirect()->route('machines.show', ['machine' => $request->machine_id]);
    }

    public function updateWithMedia(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'maintenance_type' => 'required',
            'problems' => $request->maintenance_type == 'Correctivo' ? 'required' : 'nullable' . '|string',
            'actions' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'responsible' => 'required|string',
            'machine_id' => 'required|numeric',
        ]);

        $maintenance->update($request->all());
        // update image
        $maintenance->clearMediaCollection();
        $maintenance->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());

        event(new RecordEdited($maintenance));

        return redirect()->route('machines.show', ['machine' => $request->machine_id]);
    }


    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        event(new RecordDeleted($maintenance));
    }
}
