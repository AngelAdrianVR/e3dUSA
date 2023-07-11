<?php

namespace App\Http\Controllers;

use App\Http\Resources\DesignResource;
use App\Models\Company;
use App\Models\Design;
use App\Models\DesignType;
use App\Models\User;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    
    public function index()
    {
        $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->latest()->get());

        return inertia('Design/Index', compact('designs'));
    }

    
    public function create()
    {
        $designers = User::all();
        $design_types = DesignType::all();
        $companies = Company::all();

        return inertia('Design/Create', compact('designers', 'design_types', 'companies'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'company_branch_name' => 'required',
            'contact_name' => 'nullable',
            'designer_id' => 'required',
            'name' => 'required',
            'design_type_id' => 'required',
            'dimensions' => 'nullable',
            'measure_unit' => 'required',
            'pantones' => 'nullable',
            'specifications' => 'required',
        ]);

        Design::create($request->except('original_design_id') + [
            'user_id' => auth()->id()
        ]);

        return to_route('designs.index');
    }

    
    public function show(Design $design)
    {
        $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->latest()->get());
        return inertia('Design/Show', compact('design', 'designs'));
    }

    
    public function edit(Design $design)
    {
        $designers = User::all();
        $design_types = DesignType::all();
        $companies = Company::all();

        return inertia('Design/Edit',compact('design', 'designers', 'design_types', 'companies'));
    }

    
    public function update(Request $request, Design $design)
    {
        $request->validate([
            'company_branch_name' => 'required',
            'contact_name' => 'nullable',
            'designer_id' => 'required',
            'name' => 'required',
            'design_type_id' => 'required',
            'dimensions' => 'nullable',
            'measure_unit' => 'required',
            'pantones' => 'nullable',
            'specifications' => 'required',
        ]);

        $design->update($request->except('original_design_id') + [
            'user_id' => auth()->id()
        ]);

        return to_route('designs.index');
    }

    
    public function destroy(Design $design)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->companies as $company) {
            $company = Design::find($company['id']);
            $company?->delete();
        }

        return response()->json(['message' => 'Cliente(s) eliminado(s)']);
    }

    public function startOrder(Request $request, Design $design)
    {
        $request->validate([
            'expected_end_at' => 'required|date|after:yesterday'
        ]);

        $design->update([
            'expected_end_at' => $request->expected_end_at,
            'started_at' => now()
        ]);

       return to_route('designs.show', ['design'=> $design]);
    }

    public function finishOrder(Design $design)
    {
        $design->update([
            'finished_at' => now()
        ]);

       return to_route('designs.show', ['design'=> $design]);
    }
}
