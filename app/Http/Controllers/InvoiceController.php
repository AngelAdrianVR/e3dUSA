<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        // $invoices = Invoice::latest()->paginate(30);
        $invoices = null;

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
            'invoice_amount' => 'required|numeric|max:10',
            'currency' => 'nullable|string',
            'payment_option' => 'nullable',
            'payment_method' => 'nullable',
            'status' => 'nullable',
            'notes' => 'nullable|string|max:800',
            'company_branch_id' => 'required',
            'sale_id' => 'required',
            'invoice_quantity' => 'required',
            'complements' => 'nullable',
            'extra_invoices' => 'nullable',
        ]);

        $invoice = Invoice::create($request->all() + ['created_by' => auth()->user()->name]);

        // Agrega el archivo adjunto a una coleccion llamafa factura
        if ($request->hasFile('media')) {
            $invoice
                ->addMedia($request->file('media'))
                ->toMediaCollection('factura');
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
}
