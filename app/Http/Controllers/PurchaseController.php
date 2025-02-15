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
                'products' => $purchase->products,
                'emited_at' => $purchase->emited_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'Pedido no realizado',
                'recieved_at' => $purchase->recieved_at?->isoFormat('YYYY MMM DD') ?? 'No recibido',
                'supplier_name' => $purchase->supplier?->name,
                'supplier_nickname' => $purchase->supplier?->nickname,
                'created_at' => $purchase->created_at?->isoFormat('DD MMMM YYYY, h:mm A'),
            ];
        });

        if (auth()->user()->can('Ver info sensible de ordenes de compra')) {
            return inertia('Purchase/Index', compact('purchases'));
        } else {
            return inertia('Purchase/IndexLimited', compact('purchases'));
        }
    }

    public function create()
    {
        $suppliers = Supplier::get(['id', 'name', 'nickname']);

        if (auth()->user()->can('Ver info sensible de ordenes de compra')) {
            return inertia('Purchase/Create', compact('suppliers'));
        } else {
            return inertia('Purchase/CreateLimited', compact('suppliers'));
        }
    }

    public function store(Request $request)
    {
        $high_privileges = auth()->user()->can('Ver info sensible de ordenes de compra');
        $validation = $request->validate([
            'notes' => 'nullable',
            'expected_delivery_date' => 'nullable|date|after:yesterday',
            'supplier_id' => 'required',
            'is_iva_included' => 'boolean',
            'is_spanish_template' => 'boolean',
            'show_prices' => 'boolean',
            'contact_id' => $high_privileges ? 'required' : 'nullable',
            'products' => 'array|min:1',
            'currency' => 'required|string',
            'bank_information' => $high_privileges ? 'required' : 'nullable',
        ]);

        $purchase = Purchase::create($validation + ['user_id' => auth()->user()->id]);
        $can_authorize = auth()->user()->can('Autorizar ordenes de compra') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $purchase->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify Maribel
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

        if (auth()->user()->can('Ver info sensible de ordenes de compra')) {
            return inertia('Purchase/Show', compact('purchase', 'purchases'));
        } else {
            return inertia('Purchase/ShowLimited', compact('purchase', 'purchases'));
        }
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::get(['id', 'name', 'nickname']);

        if (auth()->user()->can('Ver info sensible de ordenes de compra')) {
            return inertia('Purchase/Edit', compact('purchase', 'suppliers'));
        } else {
            return inertia('Purchase/EditLimited', compact('purchase', 'suppliers'));
        }
    }

    public function update(Request $request, Purchase $purchase)
    {
        $high_privileges = auth()->user()->can('Ver info sensible de ordenes de compra');
        $validation = $request->validate([
            'notes' => 'nullable',
            'expected_delivery_date' => 'nullable|date|after:yesterday',
            'supplier_id' => 'required',
            'is_iva_included' => 'boolean',
            'is_spanish_template' => 'boolean',
            'show_prices' => 'boolean',
            'contact_id' => $high_privileges ? 'required' : 'nullable',
            'currency' => 'required|string',
            'products' => 'nullable|min:1',
            'bank_information' => $high_privileges ? 'required' : 'nullable',
        ]);

        $purchase->update($validation);

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
    }

    public function markOrderRecieved(Purchase $currentPurchase)
    {
        $currentPurchase->recieved_at = now();
        $currentPurchase->status = 3; //3. Recibido
        $currentPurchase->save();
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

    public function storeRating(Purchase $purchase, Request $request)
    {
        // guardar la evaluacion
        $rating = [];
        $rating["created_by"] = auth()->user()->name;
        $rating["created_at"] = now()->toDateTimeString();
        $rating["questions"] = $this->getProcessedQuestions($request->all());
        $purchase->rating = $rating;

        // marcar como recibio
        $purchase->recieved_at = now();
        $purchase->status = 3;
        $purchase->save();

        // return response()->json([]);
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

        // ver en navegador
        return $pdf->stream('reporte.pdf');

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
            $user = auth()->user();

            if ($user->email_password) {
                // Guardar las credenciales de correo actuales del .env
                $previousMailConfig = [
                    'MAIL_MAILER' => config('mail.mailer'),
                    'MAIL_HOST' => config('mail.host'),
                    'MAIL_PORT' => config('mail.port'),
                    'MAIL_USERNAME' => config('mail.username'),
                    'MAIL_PASSWORD' => config('mail.password'),
                    'MAIL_ENCRYPTION' => config('mail.encryption'),
                    'MAIL_FROM_ADDRESS' => config('mail.from.address'),
                    'MAIL_FROM_NAME' => config('mail.from.name'),
                ];

                // Configurar las credenciales de correo del usuario autenticado
                config([
                    'mail.mailer' => 'smtp',
                    'mail.host' => 'smtp.ionos.mx',
                    'mail.port' => 465,
                    'mail.username' => $user->email,
                    'mail.password' => $user->email_password,
                    'mail.encryption' => 'ssl',
                    'mail.from.address' => $user->email,
                    'mail.from.name' => $user->name,
                ]);
            }

            // Enviar correo
            Mail::to($contact->email)
                ->bcc([$user->email])
                ->send(new EmailSupplierTemplateMarkdownMail($request->subject, $request->content, $attach));

            if ($user->email_password) {
                // Restaurar las credenciales de correo originales del .env
                config($previousMailConfig);
            }
        }

        return response()->json([]);
    }

    // public function pdfTest()
    // {
    //     // crear pdf
    //     $purchase = Purchase::with(['supplier.contacts'])->first();
    //     $products = $purchase->products;
    //     $raw_materials_ids = [];
    //     foreach ($products as $product) {
    //         $raw_materials_ids[] = $product['id'];
    //     }

    //     $raw_materials = RawMaterial::with('media')->whereIn('id', $raw_materials_ids)->get([
    //         'id',
    //         'name',
    //         'part_number',
    //         'description',
    //         'measure_unit',
    //         'cost'
    //     ]);

    //     $pdf = Pdf::loadView('pdfTemplates.purchase-order', compact('purchase', 'raw_materials'));

    //     // ver en navegador
    //     return $pdf->stream('reporte.pdf');
    // }

    private function getProcessedQuestions($answers)
    {
        $result = [];

        // Pregunta 1
        $lateDays = (int) $answers['q1_days'];
        if ($lateDays == 0) {
            $points = 40;
        } elseif ($lateDays == 1) {
            $points = 30;
        } elseif ($lateDays >= 2 && $lateDays <= 3) {
            $points = 20;
        } elseif ($lateDays >= 4 && $lateDays <= 5) {
            $points = 10;
        } else {
            $points = 0; // 6 o más días, 0 punts
        }
        $result[] = ['answer' => $answers['q1'], 'points' => $points];

        // Pregunta 2
        if ($answers['q2'] === 'Sí, cumplió con todo') {
            $points = 15;
        } elseif ($answers['q2'] === 'No, no se cumplieron las especificaciones') {
            $points = 0;
        }
        $result[] = ['answer' => $answers['q2'], 'points' => $points];

        // Pregunta 3
        if (is_null($answers['q3_2']) || $answers['q3_2'] === 'Atención inmediata') {
            $points = 15;
        } elseif ($answers['q3_2'] === 'No brindó soporte') {
            $points = 0;
        } elseif ($answers['q3_1'] === 'Soporte por defecto del producto' && $answers['q3_2'] === 'Atención tardía (2 o más días para atender)') {
            $points = 5;
        } elseif ($answers['q3_1'] === 'Soporte de acuerdo al desarrollo del material' && $answers['q3_2'] === 'Atención tardía (2 o más días para atender)') {
            $points = 10;
        }
        $result[] = ['answer' => $answers['q3_1'], 'points' => $points];

        // Pregunta 4
        if ($answers['q4'] === 'No se presentó ninguna urgencia') {
            $points = 15;
        } elseif ($answers['q4'] === '1 día de atraso') {
            $points = 10;
        } elseif ($answers['q4'] === '2 a 3 días de atraso') {
            $points = 7.5;
        } elseif ($answers['q4'] === '4 a 5 días de atraso') {
            $points = 5;
        } elseif ($answers['q4'] === 'Más de 6 días de atraso') {
            $points = 0;
        }
        $result[] = ['answer' => $answers['q4'], 'points' => $points];

        // Pregunta 5
        if ($answers['q5'] === '0 avisos de rechazo') {
            $points = 15;
        } elseif ($answers['q5'] === '1 aviso de rechazo') {
            $points = 10;
        } elseif ($answers['q5'] === '2 o más avisos de rechazo') {
            $points = 0;
        }
        $result[] = ['answer' => $answers['q5'], 'points' => $points];

        return $result;
    }
}
