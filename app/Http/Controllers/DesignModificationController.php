<?php

namespace App\Http\Controllers;

use App\Models\DesignModification;
use Illuminate\Http\Request;

class DesignModificationController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
       
    }

    public function store(Request $request)
    {
        $request->validate([
            'modifications' => 'required|string|max:800',
            'design_id' => 'required|numeric|min:1',
        ]);

        $modification = DesignModification::create($request->all());

        return response()->json(['item' => $modification]);
    }

    public function show(DesignModification $designModification)
    {
        //
    }

    public function edit(DesignModification $designModification)
    {
        //
    }

    public function update(Request $request, DesignModification $designModification)
    {
        //
    }

    public function destroy(DesignModification $designModification)
    {
        //
    }

    public function uploadResults(Request $request)
    {
        $modification = DesignModification::find($request->modification_id);
        $modification->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return response()->json(['item' => DesignModification::with('media')->find($modification->id)]);
    }
}
