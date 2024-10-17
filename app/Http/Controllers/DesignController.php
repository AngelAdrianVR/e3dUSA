<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\DesignResource;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Design;
use App\Models\DesignType;
use App\Models\Prospect;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\DesignCompletedNotification;
use App\Notifications\RequestApprovedNotification;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de diseño todas')) {

            //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
            $pre_designs = Design::with('user:id,name', 'designer:id,name', 'designType:id,name')
                ->latest()
                ->get(['id', 'name', 'created_at', 'user_id', 'designer_id', 'design_type_id', 'authorized_at', 'started_at', 'finished_at', 'has_priority']);

            $designs = $this->processDataIndex($pre_designs);

            return inertia('Design/Admin', compact('designs'));
        } else if (auth()->user()->can('Ordenes de diseño personal')) {

            //trae diseños de creadores y trabajadores
            $pre_designs = Design::with('user:id,name', 'designer:id,name', 'designType:id,name')
                ->where('user_id', auth()->id())
                ->orWhere('designer_id', auth()->id())
                ->latest()
                ->get(['id', 'name', 'created_at', 'user_id', 'designer_id', 'design_type_id', 'authorized_at', 'started_at', 'finished_at', 'has_priority']);

            $designs = $this->processDataIndex($pre_designs);

            return inertia('Design/Index', compact('designs'));
        } else {

            //solo para los empleados
            $pre_designs = Design::with('user:id,name', 'designer:id,name', 'designType:id,name')
                ->whereNotNull('authorized_at')
                ->where('designer_id', auth()->id())
                ->latest()
                ->get(['id', 'name', 'created_at', 'user_id', 'designer_id', 'design_type_id', 'authorized_at', 'started_at', 'finished_at', 'has_priority']);

            $designs = $this->processDataIndex($pre_designs);

            return inertia('Design/Index', compact('designs'));
        }
    }

    public function processDataIndex($pre_designs)
    {
        $designs = $pre_designs->map(function ($design) {
            $status = [
                'label' => 'Esperando Autorización',
                'text-color' => 'text-amber-500',
                'border-color' => 'border-amber-500'
            ];

            if ($design->authorized_at) {
                $status = [
                    'label' => 'Autorizado. Sin iniciar',
                    'text-color' => 'text-amber-700',
                ];
                if ($design->started_at) {
                    $status = [
                        'label' => 'En proceso',
                        'text-color' => 'text-[#0355B5]',
                    ];
                    if ($design->finished_at) {
                        $status = [
                            'label' => 'Terminado',
                            'text-color' => 'text-green-600',
                        ];
                    }
                }
            }
            return [
                'id' => $design->id,
                'name' => $design->name,
                'user' => [
                    'id' => $design->user->id,
                    'name' => $design->user->name
                ],
                'designer' => [
                    'id' => $design->designer->id,
                    'name' => $design->designer->name
                ],
                'design' => $design->name,
                'has_priority' => $design->has_priority,
                'design_type' => $design->designType,
                'status' => $status,
                'created_at' => $design->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            ];
        });

        return $designs;
    }

    public function create()
    {
        $designers = User::where('is_active', 1)->where('employee_properties->department', 'Diseño')->get();
        $design_types = DesignType::all();
        $company_branches = CompanyBranch::with('contacts:id,contactable_id,contactable_type,name,email,charge')->latest()->get(['id', 'name']);
        $prospects = Prospect::all(['id', 'name', 'contact_name']);

        return inertia('Design/Create', compact('designers', 'design_types', 'company_branches', 'prospects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_branch_name' => 'required',
            'contact_name' => 'nullable',
            'designer_id' => 'required',
            'name' => 'required|string|max:255',
            'design_type_id' => 'required',
            'dimensions' => 'nullable',
            'measure_unit' => 'required',
            'pantones' => 'nullable|string|max:255',
            'specifications' => 'required|string|max:800',
        ]);

        $design = Design::create($request->except('original_design_id') + [
            'user_id' => auth()->id()
        ]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de diseño') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $design->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de diseño', 'designs.index'));
        }

        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('media_logo')) {
            $design->addMediaFromRequest('media_logo')
                ->preservingOriginal() // Preserva el archivo original
                ->toMediaCollection('logo');
        }

        // Guardar el archivo en la colección 'plano'
        if ($request->hasFile('media_plano')) {
            $design->addMediaFromRequest('media_plano')
                ->preservingOriginal() // Preserva el archivo original
                ->toMediaCollection('plano');
        }

        event(new RecordCreated($design));

        return to_route('designs.show', $design->id);
    }

    public function show($design_id)
    {
        if (auth()->user()->hasRole('Super admin') || auth()->user()->can('Ordenes de diseño todas')) {
            $design = DesignResource::make(Design::with('user', 'designer', 'designType', 'modifications.media')->find($design_id));
            $pre_designs = Design::latest()->get();
            $designs = $pre_designs->map(function ($design) {
                return [
                    'id' => $design->id,
                    'name' => $design->name,
                ];
            });
            // return $design;
            return inertia('Design/Show', compact('design', 'designs'));
        } elseif (auth()->user()->can('Ordenes de diseño personal')) {
            $design = DesignResource::make(Design::with('user', 'designer', 'designType', 'modifications.media')->find($design_id));
            $pre_designs = DesignResource::collection(Design::where('designer_id', auth()->id())->latest()->get());
            $designs = $pre_designs->map(function ($design) {
                return [
                    'id' => $design->id,
                    'name' => $design->name,
                ];
            });
            return inertia('Design/Show', compact('design', 'designs'));
        } else {
            $design = DesignResource::make(Design::with('user', 'designer', 'designType', 'modifications.media')->find($design_id));
            $pre_designs = DesignResource::collection(Design::where('designer_id', auth()->id())->latest()->get());
            $designs = $pre_designs->map(function ($design) {
                return [
                    'id' => $design->id,
                    'name' => $design->name,
                ];
            });
            return inertia('Design/Show', compact('design', 'designs'));
        }
    }

    public function edit(Design $design)
    {
        $designers = User::where('is_active', 1)->where('employee_properties->department', 'Diseño')->get();
        $design_types = DesignType::all();
        $prospects = Prospect::all(['id', 'name', 'contact_name']);
        $company_branches = CompanyBranch::with('contacts:id,contactable_id,contactable_type,name,email,charge')->latest()->get(['id', 'name']);
        $media_logo_url = $design->getFirstMediaUrl('logo');
        $media_plane_url = $design->getFirstMediaUrl('plano');

        return inertia('Design/Edit', compact('design', 'designers', 'design_types', 'company_branches', 'prospects', 'media_logo_url', 'media_plane_url'));
    }

    public function update(Request $request, Design $design)
    {
        $request->validate([
            'company_branch_name' => 'required',
            'contact_name' => 'nullable',
            'designer_id' => 'required',
            'name' => 'required|string|max:255',
            'design_type_id' => 'required',
            'dimensions' => 'nullable',
            'measure_unit' => 'required',
            'pantones' => 'nullable|string|max:255',
            'specifications' => 'required|string|max:800',
        ]);

        $design->update($request->except('original_design_id'));

        event(new RecordEdited($design));

        return to_route('designs.show', $design->id);
    }

    public function updateWithMedia(Request $request, Design $design)
    {
        $request->validate([
            'company_branch_name' => 'required',
            'contact_name' => 'nullable',
            'designer_id' => 'required',
            'name' => 'required|string|max:255',
            'design_type_id' => 'required',
            'dimensions' => 'nullable',
            'measure_unit' => 'required',
            'pantones' => 'nullable|string|max:255',
            'specifications' => 'required|string|max:800',
        ]);

        $design->update($request->except('original_design_id'));

        // update image
        $design->clearMediaCollection('plano');
        $design->clearMediaCollection('logo');

        // Guardar el archivo en la colección 'plano'
        if ($request->hasFile('media_plano')) {
            $design->addMediaFromRequest('media_plano')
                ->preservingOriginal() // Preserva el archivo original
                ->toMediaCollection('plano');
        }
        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('media_logo')) {
            $design->addMediaFromRequest('media_logo')
                ->preservingOriginal() // Preserva el archivo original
                ->toMediaCollection('logo');
        }
        $design->save();

        event(new RecordEdited($design));

        return to_route('designs.show', $design->id);
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

            event(new RecordDeleted($design));
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

        return response()->json(['item' => DesignResource::make($design)]);
    }

    public function finishOrder(Request $request)
    {
        $design = Design::find($request->design_id);
        $design->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('results'));
        $design->update([
            'finished_at' => now()
        ]);

        // notificar a solicitante
        $design->user->notify(new DesignCompletedNotification(auth()->user()->name, $design->name, 'design'));

        $media = $design->getMedia('results')->all();

        return response()->json(['item' => $media]);
    }

    public function authorizeOrder(Design $design)
    {
        $design->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        // notify to requester user
        $design->user->notify(new RequestApprovedNotification('Diseño', $design->name, "", 'design'));

        return response()->json(['item' => DesignResource::make($design)]);
    }

    public function fetchFiltered($filter)
    {
        if ($filter == 'Mis órdenes') {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->whereNotNull('authorized_at')->where('designer_id', auth()->id())->latest()->get());
        } else {
            $designs = DesignResource::collection(Design::with('user', 'designer', 'designType')->where('user_id', auth()->id())->latest()->get());
        }

        return response()->json($designs);
    }
}
