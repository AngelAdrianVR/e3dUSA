<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DesignType;
use App\Models\ExclusiveDesign;
use Illuminate\Http\Request;

class ExclusiveDesignController extends Controller
{
   
    public function index()
    {
        //
    }

    public function create(Request $request)
    {
        $company = Company::find($request->company_id);
        $design_types = DesignType::all();

        return inertia('ExclusiveDesign/Create', compact('company', 'design_types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:650',
            'company_id' => 'required|numeric|min:1',
            'media' => 'required|array|min:1',
        ]);

        $ed = ExclusiveDesign::create($validated + ['user_id' => auth()->id()]);

        // archivo
        $ed->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('companies.show', $validated['company_id']);
    }

    public function show(ExclusiveDesign $exclusiveDesign)
    {
        //
    }

    public function edit(ExclusiveDesign $exclusiveDesign)
    {
        //
    }

    public function update(Request $request, ExclusiveDesign $exclusiveDesign)
    {
        //
    }

    public function destroy(ExclusiveDesign $exclusiveDesign)
    {
        $exclusiveDesign->delete();

        return response()->json([]);
    }
}
