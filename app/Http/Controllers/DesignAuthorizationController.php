<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\DesignAuthorizationResource;
use App\Models\CompanyBranch;
use App\Models\DesignAuthorization;
use Illuminate\Http\Request;

class DesignAuthorizationController extends Controller
{
    public function index()
    {
        $design_authorizations = DesignAuthorization::with('companyBranch:id,name', 'seller:id,name')
            ->get(['id', 'name', 'version', 'authorized_at', 'responded_at', 'design_accepted', 'rejected_razon', 'seller_id', 'company_branch_id']);

        // return $design_authorizations;
        return inertia('DesignAuthorization/Index', compact('design_authorizations'));
    }
    
    public function create()
    {
        $company_branches = CompanyBranch::with('contacts')->get(['id', 'name', 'company_id']);
        $company_id = request('company_id');
        $company_id_numero = intval($company_id);

        return inertia('DesignAuthorization/Create', compact('company_branches', 'company_id'));
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

        event(new RecordCreated($design_authorization));

        return to_route('design-authorizations.show', ['design_authorization' => $design_authorization->id ]);
    }

    public function show($design_authorization_id)
    {
        $design_authorization = DesignAuthorizationResource::make(DesignAuthorization::with(['seller:id,name', 'companyBranch:id,name', 'companyBranch.contacts'])->find($design_authorization_id));

        // return $design_authorization;
        return inertia('DesignAuthorization/Show', compact('design_authorization'));
    }

    public function edit($design_authorization_id)
    {
        $company_branches = CompanyBranch::with('contacts')->get(['id', 'name']);
        $design_authorization = DesignAuthorization::with('media')->find($design_authorization_id);

        return inertia('DesignAuthorization/Edit', compact('design_authorization', 'company_branches'));
    }

    public function update(Request $request, DesignAuthorization $design_authorization)
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

        $design_authorization->update($request->except('contact_charge'));

        // Eliminar imágenes antiguas solo si se borró desde el input y no se agregó una nueva
        if ($request->imageCleared) {
            $design_authorization->clearMediaCollection('image');
        }

        event(new RecordEdited($design_authorization));

        return to_route('design-authorizations.show', ['design_authorization' => $design_authorization->id ]);
    }   

    public function updateWithMedia(Request $request, DesignAuthorization $design_authorization)
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

        $design_authorization->update($request->except('contact_charge'));

         // media ---------------------------------------------------
        // Eliminar imagen antigua solo si se proporciona nueva imagen
        if ($request->hasFile('image')) {
            $design_authorization->clearMediaCollection('image');
        }

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('image')) {
            $design_authorization->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // event(new RecordEdited($design_authorization));

        return to_route('design-authorizations.show', ['design_authorization' => $design_authorization->id ]);
    }

    public function destroy(DesignAuthorization $design_authorization)
    {
        $design_authorization->delete();
    }

    public function AuthorizeDesign(DesignAuthorization $design_authorization)
    {
        $design_authorization->update([
            'authorized_at' => now(),
        ]);

        return response()->json(['authorized_at' => $design_authorization->authorized_at]);
    }

    public function fetchForCompanyBranch($company_branch_id)
    {
        $design_authorizations = DesignAuthorization::where('company_branch_id', $company_branch_id)->where('design_accepted', 1)->get(['id', 'name']);

        return response()->json(['items' =>  $design_authorizations]);
    }

    public function print($design_authorization)
    {
        $design_authorization = DesignAuthorizationResource::make(DesignAuthorization::with(
        ['seller:id,name', 'companyBranch:id,name', 'companyBranch.contacts'])
            ->find($design_authorization));

        return inertia('DesignAuthorization/Print', compact('design_authorization'));
    }

}
