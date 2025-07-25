<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Calendar;
use App\Models\Invoice;
use App\Models\ProgramedInvoice;
use App\Models\Sale;
use App\Notifications\ScheduleCreateInvoiceReminder;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('companyBranch:id,name')
            ->select(['id', 'folio', 'created_at', 'created_by', 'invoice_quantity', 'company_branch_id', 'invoice_amount', 'complements', 'sale_id', 'status', 'number_of_invoice'])
            ->latest()
            ->paginate(30);

            // return $invoices;
        return inertia('Invoice/Index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $sale_id = $request->input('sale_id');
        $total_amount_sale = $request->input('total_amount_sale');
        $invoice_quantity = $request->input('invoice_quantity');

        // Query base
        $salesQuery = Sale::with('companyBranch:id,name')
            ->latest()
            ->take(300)
            ->select(['id', 'company_branch_id']);

        // Solo aplicar filtro si NO hay sale_id (es decir, creación de factura normal)
        // si la creacion es desde el show para factura extra en la misma ov entonces si se muestran todas
        if (!$sale_id) {
            $salesQuery->doesntHave('invoices');
        }

        $sales = $salesQuery->get();

        return inertia('Invoice/Create', [
            'sales' => $sales,
            'sale_id' => $sale_id,
            'total_amount_sale' => $total_amount_sale,
            'invoice_quantity' => $invoice_quantity,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'folio' => 'required|string',
            'issue_date' => 'required|date',
            'total_amount_sale' => 'nullable',
            'invoice_amount' => 'required|numeric|max:999999',
            'currency' => 'nullable|string',
            'payment_option' => 'nullable',
            'payment_method' => 'nullable',
            'status' => 'nullable',
            'notes' => 'nullable|string|max:800',
            'company_branch_id' => 'required',
            'sale_id' => 'required',
            'invoice_quantity' => 'required',

            'complements' => 'nullable|array',
            'complements.*.folio' => 'required|string',
            'complements.*.payment_date' => 'required|date',
            'complements.*.amount' => 'required|numeric',
            'complements.*.payment_method' => 'required|string',
            'complements.*.notes' => 'nullable|string|max:800',

            'extra_invoices' => 'nullable|array',
        ]);

        $same_ov_invoices_quantity = Invoice::where('sale_id', $request->sale_id)->count();

        $invoice = Invoice::create($request->all() + [
            'created_by' => auth()->user()->name,
            'number_of_invoice' => $same_ov_invoices_quantity + 1, // posicion de factura (en caso de tener mas facturas la misma ov se indica el numero de factura o posición)
        ]);

        // Agrega el archivo adjunto a una coleccion llamafa factura
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $invoice
                    ->addMedia($file)
                    ->toMediaCollection('factura');
            }
        }

        //Agrega los archivos de los complementos iterandolos 1 x 1
        foreach ($request->complements ?? [] as $complementIndex => $complementData) {
            if (isset($complementData['complementMedia']) && is_array($complementData['complementMedia'])) {
                foreach ($complementData['complementMedia'] as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $invoice
                            ->addMedia($file)
                            ->usingName('Complemento ' . ($complementIndex + 1))
                            ->toMediaCollection('complementos');
                    }
                }
            }
        }

        // crea recordatorios de facturas programadas en caso de que haya
        if ($request->extra_invoices) {
            $user = auth()->user();

            foreach ($request->extra_invoices as $key => $reminderData) {
                // Omitir el primer registro (índice 0) porque es la factura ya creada. las otras son las programadas
                if ($key === 0) {
                    continue;
                }

                ProgramedInvoice::create([
                    'programed_by' => $user->name,
                    'reminder_date' => $reminderData['reminder_date'],
                    'reminder_time' => $reminderData['reminder_time'],
                    'amount' => $reminderData['invoice_amount'],
                    'number_of_invoice' => $key + 1,
                    'sale_id' => $invoice->sale_id,
                    'company_branch_id' => $invoice->company_branch_id,
                ]);

                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => 'Factura programada para OV-' . $request->sale_id . '. Para fecha ' . $reminderData['reminder_date'] . ' a las ' . $reminderData['reminder_time'],
                    'repeater' => 'No se repite',
                    'description' => 'Se programó una factura relacionada a la OV-' . $request->sale_id,
                    'status' => 'Pendiente',
                    'start_date' => $reminderData['reminder_date'],
                    'start_time' => $reminderData['reminder_time'],
                    'user_id' => $user->id,
                ]);

                $user->notify(new ScheduleCreateInvoiceReminder($reminder));
            }
        }

        return to_route('invoices.show', $invoice);
    }

    public function show(Invoice $invoice)
    {   
        $invoices = Invoice::latest()->get(['id', 'folio']);
        $invoice->load('media', 'companyBranch.company', 'companyBranch.contacts');

        // Cargar facturas del mismo sale_id, excepto la actual
        $extra_invoices = Invoice::where('sale_id', $invoice->sale_id)
            ->where('id', '!=', $invoice->id)
            ->latest()
            ->get();

        // return $extra_invoices;
        return inertia('Invoice/Show', compact('invoice', 'invoices', 'extra_invoices'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('media');

        // Query base
        $sales = Sale::with('companyBranch:id,name')
            ->latest()
            // ->doesntHave('invoices')
            ->take(300)
            ->select(['id', 'company_branch_id'])
            ->get();

        return inertia('Invoice/Edit', [
            'sales' => $sales,
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'folio' => 'required|string',
            'issue_date' => 'required|date',
            'total_amount_sale' => 'nullable',
            'invoice_amount' => 'required|numeric|max:999999',
            'currency' => 'nullable|string',
            'payment_option' => 'nullable',
            'payment_method' => 'nullable',
            'status' => 'nullable',
            'notes' => 'nullable|string|max:800',
            'company_branch_id' => 'required',
            'sale_id' => 'required',
            'invoice_quantity' => 'required',

            'complements' => 'nullable|array',
            'complements.*.folio' => 'required|string',
            'complements.*.payment_date' => 'required|date',
            'complements.*.amount' => 'required|numeric',
            'complements.*.payment_method' => 'required|string',
            'complements.*.notes' => 'nullable|string|max:800',

            'extra_invoices' => 'nullable|array',
        ]);

        $invoice->update($request->all());

        // Agrega el archivo adjunto a una coleccion llamafa factura
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $invoice
                    ->addMedia($file)
                    ->toMediaCollection('factura');
            }
        }

        //Agrega los archivos de los complementos iterandolos 1 x 1
        foreach ($request->complements ?? [] as $complementIndex => $complementData) {
            if (isset($complementData['complementMedia']) && is_array($complementData['complementMedia'])) {
                foreach ($complementData['complementMedia'] as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $invoice
                            ->addMedia($file)
                            ->usingName('Complemento ' . ($complementIndex + 1))
                            ->toMediaCollection('complementos');
                    }
                }
            }
        }

        // crea recordatorios de facturas programadas en caso de que haya
        if ($request->extra_invoices) {
            $user = auth()->user();

            foreach ($request->extra_invoices as $key => $reminderData) {
                // Omitir el primer registro (índice 0) porque es la factura ya creada. las otras son las programadas
                if ($key === 0) {
                    continue;
                }

                $reminder = Calendar::create([
                    'type' => 'Tarea',
                    'title' => 'Factura programada para OV-' . $request->sale_id . '. Para fecha ' . $reminderData['reminder_date'] . ' a las ' . $reminderData['reminder_time'],
                    'repeater' => 'No se repite',
                    'description' => 'Se programó una factura relacionada a la OV-' . $request->sale_id,
                    'status' => 'Pendiente',
                    'start_date' => $reminderData['reminder_date'],
                    'start_time' => $reminderData['reminder_time'],
                    'user_id' => $user->id,
                ]);

                $user->notify(new ScheduleCreateInvoiceReminder($reminder));
            }
        }

        return to_route('invoices.show', $invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->invoices as $invoice) {
            $invoice = Invoice::find($invoice['id']);
            $invoice?->delete();

            event(new RecordDeleted($invoice));
        }

        return response()->json(['message' => 'Cotización(es) eliminada(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $invoices = Invoice::latest()->with('companyBranch:id,name')
            ->where('folio', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhere('sale_id', 'like', "%{$query}%")
            ->get(['id', 'folio', 'created_at', 'created_by', 'invoice_quantity', 'company_branch_id', 'invoice_amount', 'complements', 'sale_id', 'status', 'number_of_invoice']);

        return response()->json(['items' => $invoices], 200);
    }

    public function changeStatus(Request $request, Invoice $invoice)
    {
        // Validar que se recibió un estatus válido
        $validated = $request->validate([
            'status' => 'required|string|in:Emitida,Pendiente de pago,Parcialmente pagada,Pagada,Cancelada', // ajusta los valores permitidos según tu lógica
        ]);

        // Actualizar el estatus
        $invoice->update([
            'status' => $validated['status'],
        ]);

        // Responder con el invoice actualizado
        return response()->json([
            'message' => 'Estatus actualizado correctamente.',
            'invoice' => $invoice
        ]);
    }

    public function storeComplement(Request $request, Invoice $invoice)
    {
        // Validar estructura básica del complemento
        $validated = $request->validate([
            'complements' => 'required|array',
            'complements.*.folio' => 'required|string',
            'complements.*.payment_date' => 'required|date',
            'complements.*.amount' => 'required|numeric',
            'complements.*.payment_method' => 'nullable|string',
            'complements.*.notes' => 'nullable|string',
        ]);

        // Cambia la forma de pago a pdd si se crea un complemento
        if ( $invoice->payment_option === 'PUE' ) {
            $invoice->payment_option = 'PDD';
            $invoice->save();
        }

        // Obtener los complementos actuales para guardar el complemento que se ha creado
        $existingComplements = $invoice->complements ?? [];
        $mergedComplements = array_merge($existingComplements, $validated['complements']);
        $invoice->complements = $mergedComplements;
        $invoice->save();

        // Agregar el archivo al complemento creado ------------------------------------------------------
        // Contar complementos actuales para numerar correctamente el archivo
        $existingCount = is_array($invoice->complements) ? count($invoice->complements) : 0;

        // Como solo se agrega 1 complemento por request, tomamos el primero
        $complementData = $request->complements[0] ?? null;

        if ($complementData && isset($complementData['complementMedia']) && is_array($complementData['complementMedia'])) {
            foreach ($complementData['complementMedia'] as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    // El número de este nuevo complemento es el siguiente
                    $complementNumber = $existingCount;

                    $invoice
                        ->addMedia($file)
                        ->usingName('Complemento ' . $complementNumber)
                        ->toMediaCollection('complementos');
                }
            }
        }

        // Evaluar si es una sola factura
        if ($invoice->invoice_quantity == 1) {
            $totalPaid = collect($invoice->complements)->sum('amount');
            $invoice->status = $totalPaid >= $invoice->total_amount_sale ? 'Pagada' : 'Parcialmente pagada';
            $invoice->save();
            return;
        }

        // Si tiene varias facturas:
        $saleId = $invoice->sale_id;
        $allInvoices = Invoice::where('sale_id', $saleId)
            ->orderBy('number_of_invoice')
            ->get();

        foreach ($allInvoices as $index => $factura) {
            $hasComplements = is_array($factura->complements) && count($factura->complements) > 0;
            $complementSum = $hasComplements
                ? collect($factura->complements)->sum('amount')
                : 0;

            if ($hasComplements) {
                $factura->status = $complementSum >= $factura->invoice_amount
                    ? 'Pagada'
                    : 'Parcialmente pagada';
            }

            $factura->save();
        }
        
        return;
    }

    // public function storeComplement(Request $request, Invoice $invoice)
    // {
    //     // Validar estructura básica del complemento
    //     $validated = $request->validate([
    //         'complements' => 'required|array',
    //         'complements.*.folio' => 'required|string',
    //         'complements.*.payment_date' => 'required|date',
    //         'complements.*.amount' => 'required|numeric',
    //         'complements.*.payment_method' => 'nullable|string',
    //         'complements.*.notes' => 'nullable|string',
    //     ]);

    //      // Obtener los complementos actuales si existen
    //     $existingComplements = $invoice->complements ?? [];

    //     // Unir los existentes con los nuevos
    //     $mergedComplements = array_merge($existingComplements, $validated['complements']);

    //     // Guardar
    //     $invoice->complements = $mergedComplements;
    //     $invoice->save();;
    // }


}
