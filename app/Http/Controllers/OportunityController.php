<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\OportunityResource;
use App\Http\Resources\TagResource;
use App\Models\Company;
use App\Models\Oportunity;
use App\Models\OportunityTask;
use App\Models\Project;
use App\Models\Sale;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class OportunityController extends Controller
{

    public function index()
    {
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks','company', 'companyBranch')->whereHas('users', function ($query) {
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
            'estimated_finish_date' => 'required|date|after_or_equal:start_date',
            'type_access_project' => 'nullable|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_branch_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',

        ]);

        if ($request->status == 'Cerrada' || $request->status == 'Pagado') {

            $oportunity = Oportunity::create($validated + ['user_id' => auth()->id(), 'finished_at' => now()]);
            $time = \Carbon\Carbon::createFromFormat('h A', '7 PM')->format('H:i:s'); //tiempo limite de realización de tarea en formato am y pm
             //Tarea 1. creada con estatus cerrada o pagado
             OportunityTask::create([
                'name' => 'Oportunidad cerrada y/o pagada',
                'limit_date' =>now()->addDays(2),
                'time' =>  $time,
                'finished_at' => now(),
                'description' => 'Se creó la oportunidad con estatus cerrada o pagado',
                'priority' => 'Baja',
                'reminder' => null,
                'user_id' => auth()->id(),
                'oportunity_id' => $oportunity->id,
                'asigned_id' => auth()->id(),
            ]);
        } else {

            $oportunity = Oportunity::create($validated + ['user_id' => auth()->id()]);
            $time = \Carbon\Carbon::createFromFormat('h A', '7 PM')->format('H:i:s'); //tiempo limite de realización de tarea en formato am y pm
            //Tarea 1. Contactar al cliente
            OportunityTask::create([
                'name' => 'Contactar al cliente',
                'limit_date' => now()->addDays(2),
                'time' =>  $time,
                'finished_at' => null,
                'description' => 'Tener contacto con el cliente',
                'priority' => 'Media',
                'reminder' => null,
                'user_id' => auth()->id(),
                'oportunity_id' => $oportunity->id,
                'asigned_id' => auth()->id(),
            ]);
            //Tarea 2. Mandar diseño
            OportunityTask::create([
                'name' => 'Mandar render del producto',
                'limit_date' => now()->addDays(4),
                'time' =>  $time,
                'finished_at' => null,
                'description' => 'Mandar render del producto de interés',
                'priority' => 'Media',
                'reminder' => null,
                'user_id' => auth()->id(),
                'oportunity_id' => $oportunity->id,
                'asigned_id' => auth()->id(),
            ]);
            //Tarea 3. Enviar cotización
            OportunityTask::create([
                'name' => 'Enviar cotización',
                'limit_date' => now()->addDays(6),
                'time' =>  $time,
                'finished_at' => null,
                'description' => 'Enviar cotización a cliente',
                'priority' => 'Media',
                'reminder' => null,
                'user_id' => auth()->id(),
                'oportunity_id' => $oportunity->id,
                'asigned_id' => auth()->id(),
            ]);
            //Tarea 4. Mandar muestra      
            OportunityTask::create([
                'name' => 'Mandar muestra',
                'limit_date' => now()->addDays(8),
                'time' =>  $time,
                'finished_at' => null,
                'description' => 'Mandar muestra de producto(s) al cliente',
                'priority' => 'Media',
                'reminder' => null,
                'user_id' => auth()->id(),
                'oportunity_id' => $oportunity->id,
                'asigned_id' => auth()->id(),
            ]);
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
        $oportunities = OportunityResource::collection(Oportunity::with('oportunityTasks.asigned', 'oportunityTasks.media', 'oportunityTasks.oportunity', 'oportunityTasks.user', 'user', 'clientMonitors.seller', 'clientMonitors.emailMonitor', 'clientMonitors.paymentMonitor', 'clientMonitors.mettingMonitor', 'clientMonitors.whatsappMonitor', 'oportunityTasks.comments.user', 'tags', 'media', 'survey', 'seller', 'users', 'company', 'companyBranch')->latest()->get());
        $users = User::where('is_active', true)->whereNot('id', 1)->get();
        $defaultTab = request('defaultTab');

        // return $oportunities;

        return inertia('Oportunity/Show', compact('oportunity', 'oportunities', 'users', 'defaultTab'));
    }


    public function edit(Oportunity $oportunity)
    {
        $users = User::where('is_active', true)->whereNot('id', 1)->get();
        $companies = Company::with('companyBranches.contacts')->latest()->get();
        $tags = TagResource::collection(Tag::where('type', 'crm')->get());
        $oportunity = Oportunity::with('users')->find($oportunity->id);

        // return $oportunity;

        return inertia('Oportunity/Edit', compact('users', 'companies', 'tags', 'oportunity'));
    }


    public function update(Request $request, Oportunity $oportunity)
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
            'estimated_finish_date' => 'required|date|after_or_equal:start_date',
            'type_access_project' => 'nullable|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_branch_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        if ($request->status == 'Cerrada' || $request->status == 'Pagado') {
            $oportunity->update($validated + ['finished_at' => now()]);
        } else {
            $oportunity->update($validated + ['finished_at' => null]);
        }

        // permisos
        $oportunity->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $oportunity->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $oportunity->tags()->sync($tagIds);
        
        event(new RecordEdited($oportunity));

        return to_route('oportunities.index');
        
    }

    public function updateWithMedia(Request $request, Oportunity $oportunity)
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
            'estimated_finish_date' => 'required|date|after_or_equal:start_date',
            'type_access_project' => 'nullable|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_branch_id' => $request->is_new_company ? 'nullable' : 'required',
            'company_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        $oportunity->update($validated);

        // permisos
        $oportunity->users()->detach();
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
        $oportunity->tags()->sync($tagIds);
        
        // media
        $oportunity->clearMediaCollection();
        $oportunity->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());
        
        if ($request->status == 'Cerrada' || $request->status == 'Pagado') {
            $oportunity->update($validated + ['finished_at' => now()]);
        } else {
            $oportunity->update($validated + ['finished_at' => null]);
        }
        
        event(new RecordEdited($oportunity));

        return to_route('oportunities.index');
    }

    public function destroy(Oportunity $oportunity)
    {
        $oportunity->delete();

        event(new RecordDeleted($oportunity));
    }

//-----------Revisa si la oportunidad ya tiene una orden de venta creada, si no la tiene, redirecciona al formulario para crearla
    public function createSale(Request $request, $oportunity_id)
    {
        $oportunity = Oportunity::find($oportunity_id);

        $sale = Sale::where('oportunity_id', $oportunity_id)->first(); //Busca una venta de esta oportunidad

        if ($sale != null) {
            return response()->json(['message' => 'Ya existe una venta de esta oportunidad']);
        }

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

        return response()->json(['item' => $oportunity->fresh()]);
    }
}
