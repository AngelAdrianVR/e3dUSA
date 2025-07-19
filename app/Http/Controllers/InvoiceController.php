<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(30);

        // return $invoices;
        return inertia('Invoice/Index', compact('invoices'));
    }

    public function create()
    {
        $sales = Sale::with('companyBranch:id,name')->doesntHave('invoices')->latest()->take(100)->get(['id', 'company_branch_id']);

        return inertia('Invoice/Create', compact('sales'));
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

        $invoice = Invoice::create($request->all() + ['created_by' => auth()->user()->name]);

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



        // redireccionar a index o show.
    }

    public function show(Invoice $invoice)
    {
        //
    }

    public function edit(Invoice $invoice)
    {
        //
    }

    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    public function destroy(Invoice $invoice)
    {
        //
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

}
