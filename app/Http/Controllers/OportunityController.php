<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityResource;
use App\Http\Resources\TagResource;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class OportunityController extends Controller
{

    public function index()
    {
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks')->whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })->latest()->get());

        return inertia('Oportunity/Index',  compact('oportunities'));
    }


    public function create()
    {
        $users = User::where('is_active', true)->get();
        $companies = Company::with('companyBranches.contacts')->latest()->get();
        $tags = TagResource::collection(Tag::where('type', 'crm')->get());

        return inertia('Oportunity/Create', compact('users', 'companies', 'tags'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable',
            'seller_id' => 'required',
            'tags' => 'nullable|array',
            'probability' => 'nullable|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'estimated_finish_date' => 'required|date|after:start_date',
            // 'type_access_project' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',

        ]);

        if ($request->status == 'Cerrada') {

            $oportunity = Oportunity::create($validated + ['user_id' => auth()->id(), 'finished_at' => now()]);
        } else {

            $oportunity = Oportunity::create($validated + ['user_id' => auth()->id()]);
        }

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $oportunity->users()->attach($user['id'], $allowedUser);
        }

        event(new RecordCreated($oportunity));

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $oportunity->tags()->attach($tagIds);

        // archivos adjuntos
        $oportunity->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('oportunities.index');
    }


    public function show(Oportunity $oportunity)
    {
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks.asigned', 'oportunityTasks.media', 'oportunityTasks.oportunity', 'oportunityTasks.user', 'user', 'clientMonitores.seller', 'oportunityTasks.comments.user', 'tags', 'media', 'survey', 'seller', 'users', 'company')->latest()->get());
        $users = User::where('is_active', true)->get();
        $defaultTab = request('defaultTab');

        return inertia('Oportunity/Show', compact('oportunity', 'oportunities', 'users', 'defaultTab'));
    }


    public function edit(Oportunity $oportunity)
    {
        //
    }


    public function update(Request $request, Oportunity $oportunity)
    {
        // event(new RecordEdited($oportunity));
    }


    public function destroy(Oportunity $oportunity)
    {
        $oportunity->delete();

        event(new RecordDeleted($oportunity));
    }

    public function updateStatus(Request $request, $oportunity_id)
    {
        $oportunity = Oportunity::find($oportunity_id);

        if ($request->status == 'Cerrada') {
            $oportunity->update([
                'status' => $request->status,
                'finished_at' => now(),
                'lost_oportunity_razon' => null,
            ]);
        } elseif ($request->status == 'Perdida') {
            $oportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'lost_oportunity_razon' => $request->lost_oportunity_razon,
            ]);
        } elseif ($request->status == 'Pagado') {
            if ($oportunity->finished_at !== null) {
                $oportunity->update([
                    'status' => $request->status,
                    'lost_oportunity_razon' => $request->lost_oportunity_razon,
                ]);
            } else {
                $oportunity->update([
                    'status' => $request->status,
                    'finished_at' => now(),
                    'lost_oportunity_razon' => $request->lost_oportunity_razon,
                ]);
            }
        } else {

            $oportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'lost_oportunity_razon' => null,
            ]);
        }
    }
}
