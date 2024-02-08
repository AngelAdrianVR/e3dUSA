<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\RawMaterialResource;
use App\Mail\EmailSupplierTemplateMarkdownMail;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{

    public function index()
    {
        // $purchases = PurchaseResource::collection(Purchase::with('contact', 'supplier', 'user')->latest()->get());
        //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
        $pre_purchases = PurchaseResource::collection(Purchase::with('supplier', 'user')->latest()->get());
        $purchases = $pre_purchases->map(function ($purchase) {

            if ($purchase->status == 0) {
                $status = 'Pendiente';
            } elseif ($purchase->status == 1) {
                $status = 'Autorizado';
            } elseif ($purchase->status == 2) {
                $status = 'Emitido';
            } else {
                $status = 'Recibido';
            }

            return [
                'id' => $purchase->id,
                'folio' => 'OC-' . str_pad($purchase->id, 4, "0", STR_PAD_LEFT),
                'user' => $purchase->user->name,
                'authorized_user_name' => $purchase->authorized_user_name ?? 'No autorizado',
                'status' => $status,
                'emited_at' => $purchase->emited_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'Pedido no realizado',
                'recieved_at' => $purchase->recieved_at?->isoFormat('YYYY MMM DD') ?? 'No recibido',
                'supplier_name' => $purchase->supplier?->name,
                'created_at' => $purchase->created_at?->isoFormat('DD MMMM YYYY, h:mm A'),
            ];
        });
        // return $purchases;        
        return inertia('Purchase/Index', compact('purchases'));
    }


    public function create()
    {
        $suppliers = Supplier::get(['id', 'name', 'nickname']);

        // return $suppliers;  

        return inertia('Purchase/Create', compact('suppliers'));
    }


    public function store(Request $request)
    {
        $validation = $request->validate([
            'notes' => 'nullable',
            'expected_delivery_date' => 'nullable|date|after:yesterday',
            'supplier_id' => 'required',
            'is_iva_included' => 'boolean',
            'contact_id' => 'required',
            'products' => 'nullable|min:1',
            'bank_information' => 'required',
        ]);

        $purchase = Purchase::create($validation + ['user_id' => auth()->user()->id]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de compra') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $purchase->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('orden de compra', 'purchases.index'));
        }

        event(new RecordCreated($purchase));

        return to_route('purchases.index');
    }


    public function show($purchase_id)
    {
        $purchase = PurchaseResource::make(Purchase::with('user', 'supplier', 'contact')->find($purchase_id));
        $pre_purchases = Purchase::latest()->get();
        $purchases = $pre_purchases->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'folio' => 'OC-' . str_pad($purchase->id, 4, "0", STR_PAD_LEFT),
            ];
        });

        return inertia('Purchase/Show', compact('purchase', 'purchases'));
    }


    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::get(['id', 'name', 'nickname']);

        return inertia('Purchase/Edit', compact('purchase', 'suppliers'));
    }


    public function update(Request $request, Purchase $purchase)
    {
        $validation = $request->validate([
            'notes' => 'nullable',
            'expected_delivery_date' => 'nullable|date|after:yesterday',
            'supplier_id' => 'required',
            'is_iva_included' => 'boolean',
            'contact_id' => 'required',
            'products' => 'nullable|min:1',
            'bank_information' => 'required',
        ]);

        $purchase->update($validation + ['user_id' => auth()->user()->id]);

        event(new RecordEdited($purchase));

        return to_route('purchases.index');
    }


    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
    }

    public function markOrderDone(Purchase $currentPurchase)
    {
        $currentPurchase->emited_at = now();
        $currentPurchase->status = 2; //2. Emitido
        $currentPurchase->save();

        return to_route('purchases.index');
    }

    public function markOrderRecieved(Purchase $currentPurchase)
    {
        $currentPurchase->recieved_at = now();
        $currentPurchase->status = 3; //3. Rebibido
        $currentPurchase->save();

        return to_route('purchases.index');
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->purchases as $purchase) {
            $purchase = Purchase::find($purchase['id']);
            $purchase?->delete();

            event(new RecordDeleted($purchase));
        }

        return response()->json(['message' => 'Órden(es) eliminada(s)']);
    }

    public function clone(Request $request)
    {
        $purchase = Purchase::find($request->purchase_id);

        $clone = $purchase->replicate()->fill([
            'folio' => $purchase->folio . ' (Copia)',
        ]);

        $clone->save();



        return response()->json(['message' => "Órden de compra clonada: {$clone->creator}", 'newItem' => PurchaseResource::make($clone)]);
    }

    public function authorizePurchase(Purchase $purchase)
    {
        $purchase->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
            'status' => 1,
        ]);

        return response()->json(['message' => 'Compra autorizadda', 'item' => $purchase]); //en caso de actualizar en la misma vista descomentar
        // return to_route('quotes.index'); // en caso de mandar al index, descomentar.
    }

    public function showTemplate($purchase_id)
    {
        $purchase = Purchase::with(['supplier.contacts'])->find($purchase_id);
        $products = $purchase->products;
        $raw_materials_ids = [];
        foreach ($products as $product) {
            $raw_materials_ids[] = $product['id'];
        }

        $raw_materials = RawMaterial::with('media')->whereIn('id', $raw_materials_ids)->get([
            'id',
            'name',
            'part_number',
            'description',
            'measure_unit',
            'cost'
        ]);

        return inertia('Purchase/Template', compact('purchase', 'raw_materials'));
    }

    public function updateQuantity(Purchase $purchase, Request $request)
    {
        $products = $purchase->products;
        // Buscar el índice del elemento con el ID deseado
        $index = array_search($request->id, array_column($products, 'id'));

        // editar cantidad
        $products[$index]['quantity'] = $request->quantity;

        // actualizar en BDD
        $purchase->update(['products' => $products]);

        return response()->json([]);
    }

    public function sendEmail(Purchase $purchase, Request $request)
    {
        // actualizar contacto e info de banco
        $purchase->update([
            'bank_information' => $request->input('bank_information'),
            'contact_id' => $request->input('contact_id'),
            'authorized_user_name' => auth()->user()->name,
            'authorized_at' => now()->toDateTimeString(),
            'status' => 2,
        ]);

        // crear pdf
        $purchase = $purchase->load(['supplier.contacts']);
        $products = $purchase->products;
        $raw_materials_ids = [];
        foreach ($products as $product) {
            $raw_materials_ids[] = $product['id'];
        }

        $raw_materials = RawMaterial::with('media')->whereIn('id', $raw_materials_ids)->get([
            'id',
            'name',
            'part_number',
            'description',
            'measure_unit',
            'cost'
        ]);

        $pdf = Pdf::loadView('pdfTemplates.purchase-order', compact('purchase', 'raw_materials'));

        $fileName = 'OC-' . str_pad($purchase->id, 4, "0", STR_PAD_LEFT);
        // Guardar el PDF en la carpeta storage/app/purchase-orders
        $content = $pdf->download()->getOriginalContent();
        $is_file_stored = Storage::put("public/purchase-orders/$fileName.pdf", $content);

        // enviar correo
        $attach = [
            'path' => $is_file_stored ? storage_path("app/public/purchase-orders/$fileName.pdf") : '',
        ];

        // obtener correo de proveedor
        if (app()->environment() === 'production') {
            $contact = Contact::find($request->contact_id);
            Mail::to($contact->email)
                ->bcc([auth()->user()->emial])
                ->send(new EmailSupplierTemplateMarkdownMail($request->subject, $request->content, $attach));
        }

        return response()->json([]);
    }
}
