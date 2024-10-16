<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Models\Sample;
use App\Http\Resources\SampleResource;
use App\Models\CatalogProduct;
use App\Models\CompanyBranch;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\RequestApprovedNotification;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    public function index()
    {
        $pre_samples = Sample::with(['catalogProduct:id,name', 'companyBranch:id,name', 'user:id,name'])->latest()
            ->paginate(20, ['id', 'name', 'quantity', 'sent_at', 'returned_at', 'comments', 'catalog_product_id', 'company_branch_id', 'user_id', 'sale_order_at', 'will_back', 'authorized_at']);

        //procesamiento de la informacion para darle formato a los atributos
        $samples = $this->processDataIndex($pre_samples);
        // return $samples;
        return inertia('Sample/Index', compact('samples'));
    }

    public function processDataIndex($pre_samples)
    {
        $samples = $pre_samples->through(function ($sample) {

            if ($sample->will_back) {

                $status = [
                    'label' => 'Enviado. Esperando regreso de muestra',
                    'bg-color' => 'bg-amber-600',
                    'text-color' => 'text-amber-600',
                    'border-color' => 'border-amber-600',
                    'description' => 'Esperando a que la muestra sea devuelta y obtengas retroalimentación',
                    'progress' => 'w-1/4'
                ];

                if ($sample->returned_at) {

                    $status = [
                        'label' => 'Muestra devuelta',
                        'bg-color' => 'bg-blue-600',
                        'text-color' => 'text-blue-600',
                        'border-color' => 'border-blue-600',
                        'description' => 'Muestra devuelta. Da seguimiento para concretar venta',
                        'progress' => 'w-1/2'
                    ];

                    if ($sample->requires_modification) {

                        $status = [
                            'label' => 'Muestra enviada de nuevo con modificación',
                            'bg-color' => 'bg-indigo-500',
                            'text-color' => 'text-indigo-600',
                            'border-color' => 'border-indigo-600',
                            'description' => 'Muestra enviada de nuevo con modificaciones. Espera retroalimentación para finalizar con el seguimiento',
                            'progress' => 'w-3/4'
                        ];
                    }
                }
            } else {

                $status = [
                    'label' => 'Enviado. Esperando respuesta',
                    'bg-color' => 'bg-amber-600',
                    'text-color' => 'text-amber-600',
                    'border-color' => 'border-amber-500',
                    'description' => 'Muestra enviada. Esperando respuesta',
                    'progress' => 'w-1/2'
                ];

                if ($sample->requires_modification) {

                    $status = [
                        'label' => 'Muestra enviada de nuevo con modificación',
                        'bg-color' => 'bg-indigo-500',
                        'text-color' => 'text-indigo-600',
                        'border-color' => 'border-indigo-600',
                        'description' => 'Muestra enviada de nuevo con modificaciones. Espera retroalimentación para finalizar con el seguimiento',
                        'progress' => 'w-3/4'
                    ];
                }
            }

            if ($sample->sale_order_at) {

                $status = [
                    'label' => 'Orden generada. Venta exitosa',
                    'bg-color' => 'bg-green-500',
                    'text-color' => 'text-green-500',
                    'border-color' => 'border-green-500',
                    'description' => '¡Venta cerrada!',
                    'progress' => 'w-full'
                ];
            } elseif ($sample->denied_at) {

                $status = [
                    'label' => 'Venta no concretada',
                    'bg-color' => 'bg-primary',
                    'text-color' => 'text-primary',
                    'border-color' => 'border-primary',
                    'description' => 'Venta no concretada',
                ];
            }

            return [
                'id' => $sample->id,
                'folio' => 'MUE-' . str_pad($sample->id, 4, "0", STR_PAD_LEFT),
                'name' => $sample->name,
                'will_back' => $sample->will_back,
                'sale_order_at' => $sample->sale_order_at?->isoFormat('DD MMM YYYY'),
                'quantity' => $sample->quantity,
                'sent_at' => $sample->sent_at?->isoFormat('DD MMM YYYY'),
                'returned_at' => $sample->returned_at?->isoFormat('DD MMM YYYY'),
                'authorized_at' => $sample->authorized_at?->isoFormat('DD MMM YYYY'),
                'comments' => $sample->comments,
                'catalog_product' => $sample->catalogProduct,
                'company_branch' => $sample->companyBranch,
                'user' => $sample->user,
                'status' => $status,
            ];
        });

        return $samples;
    }

    public function create()
    {
        $catalog_products = CatalogProduct::latest()->get(['id', 'name']);
        $pre_company_branches = CompanyBranch::with(['contacts'])->latest()->get(['id', 'name']);
        $company_branches = $pre_company_branches->map(function ($company_branch) {

            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'contacts' => $contacts,
            ];
        });

        // return $company_branches;
        return inertia('Sample/Create', compact('catalog_products', 'company_branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'will_back' => 'boolean',
            'devolution_date' => $request->will_back == false ? 'nullable|date' : 'required|date',
            'sent_at' => 'required|date',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample = Sample::create($request->except('media') + [
            'user_id' => auth()->id()
        ]);

        // Subir y asociar las imagenes
        $sample->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());

        $can_authorize = auth()->user()->can('Autorizar muestra') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $sample->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('Envio de muestra', 'samples.index'));
        }

        event(new RecordCreated($sample));

        return to_route('samples.show', $sample->id);
    }


    public function show($sample_id)
    {
        $sample = SampleResource::make(Sample::with(['user:id,name', 'companyBranch', 'catalogProduct.media'])->latest()->find($sample_id));
        $samples = Sample::latest()->get(['id', 'name']);

        // return $sample;

        return inertia('Sample/Show', compact('sample', 'samples'));
    }


    public function edit($sample_id)
    {
        $sample = Sample::latest()->with('media')->find($sample_id);
        $catalog_products = CatalogProduct::latest()->get(['id', 'name']);
        $pre_company_branches = CompanyBranch::with(['contacts'])->latest()->get(['id', 'name']);
        $company_branches = $pre_company_branches->map(function ($company_branch) {

            $contacts = $company_branch->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                ];
            });

            return [
                'id' => $company_branch->id,
                'name' => $company_branch->name,
                'contacts' => $contacts,
            ];
        });

        // return $sample;

        return inertia('Sample/Edit', compact('sample', 'catalog_products', 'company_branches'));
    }


    public function update(Request $request, Sample $sample)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'will_back' => 'boolean',
            'devolution_date' => $request->will_back ? 'required|date' : 'nullable|date',
            'sent_at' => 'required|date',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample->update($request->except('media'));

        event(new RecordEdited($sample));

        return to_route('samples.show', $sample->id);
    }

    public function updateWithMedia(Request $request, Sample $sample)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'sent_at' => 'required|date',
            'comments' => 'nullable|string',
            'catalog_product_id' => 'nullable',
            'company_branch_id' => 'required',
            'contact_id' => 'required',
            'products' => 'nullable|array|min:0',
        ]);

        $sample->update($request->all());

        // update image
        $sample->clearMediaCollection();
        $sample->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());

        event(new RecordEdited($sample));


        return to_route('samples.show', $sample->id);
    }


    public function destroy(Sample $sample)
    {
        $sample->delete();

        event(new RecordDeleted($sample));
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->samples as $sample) {
            $sample = Sample::find($sample['id']);
            $sample?->delete();

            event(new RecordDeleted($sample));
        }

        return response()->json(['message' => 'reunion(es) eliminada(s)']);
    }

    public function returned(Request $request, Sample $sample)
    {
        $request->validate([
            'comments' => 'required|string'
        ]);

        $sample->update([
            'comments' => $request->comments,
            'returned_at' => now(),
        ]);

        return to_route('samples.index');
    }

    public function saleOrder(Sample $sample)
    {
        $sample->update([
            'sale_order_at' => now(),
        ]);

        return response()->json([]);
    }

    public function finishSample(Sample $sample)
    {
        $sample->update([
            'denied_at' => now(),
        ]);

        return to_route('samples.index');
    }

    public function resentSample(Sample $sample)
    {
        $sample->update([
            'requires_modification' => true,
            'comments' => 'Muestra enviada con mofificaciones el ' . now()->isoFormat('DD MMM YYYY'),
        ]);

        return to_route('samples.index');
    }

    public function authorizeSample(Sample $sample)
    {
        $sample->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        // notify to requester user
        $sample->user->notify(new RequestApprovedNotification('Muestra', $sample->id, "Cliente {$sample->companyBranch->name}", 'sample'));

        return response()->json(['message' => 'Cotizacion autorizadda', 'authorized_at' => $sample->authorized_at?->isoFormat('DD MMM YYYY')]); //en caso de actualizar en la misma vista descomentar
        // return to_route('samples.index'); // en caso de mandar al index, descomentar.
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        $pre_samples = Sample::where('id', 'like', "%{$query}%")
            ->orWhereHas('companyBranch', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with(['catalogProduct:id,name', 'companyBranch:id,name', 'user:id,name'])->latest()
            ->paginate(50, ['id', 'name', 'quantity', 'sent_at', 'returned_at', 'comments', 'catalog_product_id', 'company_branch_id', 'user_id', 'sale_order_at', 'will_back']);

        $samples = $this->processDataIndex($pre_samples);

        return response()->json(['items' => $samples], 200);
    }
}
