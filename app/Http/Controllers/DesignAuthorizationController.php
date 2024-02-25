<?php

namespace App\Http\Controllers;

use App\Http\Resources\DesignAuthorizationResource;
use App\Models\CompanyBranch;
use App\Models\DesignAuthorization;
use Illuminate\Http\Request;

class DesignAuthorizationController extends Controller
{
    
    public function index()
    {
        // return inertia('DesignAuthorization/Index');
    }

    
    public function create()
    {
        $company_branches = CompanyBranch::with('contacts')->get(['id', 'name']);

        return inertia('DesignAuthorization/Create', compact('company_branches'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:5',
            'name' => 'required|string|max:100',
            'color' => 'required|string|max:30',
            'material' => 'required|string|max:50',
            'engrave_method' => 'nullable|string|max:50',
            'logistic' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric|min:0|max:250000',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'contact_charge' => 'nullable',
        ]);

        $design_authorization = DesignAuthorization::create($request->except('contact_charge'));

        //adjunta la imagen si es que lleva una
        if ($request->hasFile('image')) {
            $design_authorization->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // return to_route('design-authorizations.show', ['design_authorization' => $design_authorization->id ]);
    }

    
    public function show($design_authorization_id)
    {
        $design_authorization = DesignAuthorizationResource::make(DesignAuthorization::with(['seller:id,name', 'companyBranch:id,name', 'companyBranch.contacts'])->find($design_authorization_id));

        // return $design_authorization;
        return inertia('DesignAuthorization/Show', compact('design_authorization'));
    }

    
    public function edit(DesignAuthorization $design_authorization)
    {
        //
    }

    
    public function update(Request $request, DesignAuthorization $design_authorization)
    {
        //
    }

    
    public function destroy(DesignAuthorization $design_authorization)
    {
        //
    }


    public function AuthorizeDesign(DesignAuthorization $design_authorization)
    {
        $design_authorization->update([
            'authorized_at' => now(),
        ]);

        return response()->json(['authorized_at' => $design_authorization->authorized_at]);
    }

}
