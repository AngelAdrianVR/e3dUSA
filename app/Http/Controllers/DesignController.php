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
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de diseño todas')) {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->latest()->get());
            return inertia('Design/Index', compact('designs'));
        } elseif (auth()->user()->can('Ordenes de diseño personal')) {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->where('user_id', auth()->id())->latest()->get());
            return inertia('Design/Index', compact('designs'));
        } else {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->where('designer_id', auth()->id())->latest()->get());
            return inertia('Design/Index', compact('designs'));
        }
    }

    public function create()
    {
        $designers = User::where('is_active', 1)->where('employee_properties->department', 'Diseño')->get();
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

        $design = Design::create($request->except('original_design_id') + [
            'user_id' => auth()->id()
        ]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de diseño') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $design->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        }

        // Guardar el archivo en la colección 'plano'
        if ($request->hasFile('media_plano')) {
            $design->addMediaFromRequest('media_plano')->toMediaCollection('plano');
        }

        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('media_logo')) {
            $design->addMediaFromRequest('media_logo')->toMediaCollection('logo');
        }

        return to_route('designs.index');
    }


    public function show(Design $design)
    {
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de diseño todas')) {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->latest()->get());
            // return $designs;
            return inertia('Design/Show', compact('design', 'designs'));
        } elseif (auth()->user()->can('Ordenes de diseño personal')) {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->where('user_id', auth()->id())->latest()->get());
            return inertia('Design/Show', compact('design', 'designs'));
        } else {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->where('designer_id', auth()->id())->latest()->get());
            return inertia('Design/Show', compact('design', 'designs'));
        }
    }


    public function edit(Design $design)
    {
        $designers = User::all();
        $design_types = DesignType::all();
        $companies = Company::all();

        return inertia('Design/Edit', compact('design', 'designers', 'design_types', 'companies'));
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


        // Guardar el archivo en la colección 'plano'
        if ($request->hasFile('media_plano')) {
            $design->addMediaFromRequest('media_plano')->toMediaCollection('plano');
        }

        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('media_logo')) {
            $design->addMediaFromRequest('media_logo')->toMediaCollection('logo');
        }

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
        foreach ($request->designs as $design) {
            $design = Design::find($design['id']);
            $design?->delete();
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

        return to_route('designs.show', ['design' => $design]);
    }

    public function finishOrder(Request $request, Design $design)
    {
        $request->validate([
            'media' => 'nullable'
        ]);


        $design->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection('results'));

        $design->update([
            'finished_at' => now()
        ]);

        return to_route('designs.show', ['design' => $design]);
    }

    public function authorizeOrder(Design $design)
    {
        $design->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        return response()->json(['item' => DesignResource::make($design)]);
    }
}
