<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\ProgramedInvoice;
use Illuminate\Http\Request;

class ProgramedInvoiceController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(ProgramedInvoice $programmed_invoice)
    {
        //
    }

    public function edit(ProgramedInvoice $programmed_invoice)
    {
        //
    }

    public function update(Request $request, ProgramedInvoice $programmed_invoice)
    {
        $programmed_invoice->update($request->all());
    }

    public function destroy(ProgramedInvoice $programmed_invoice)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->programmedInvoices as $invoice) {
            $invoice = ProgramedInvoice::find($invoice['id']);
            $invoice?->delete();

            event(new RecordDeleted($invoice));
        }

        return response()->json(['message' => 'recordatorio(s) eliminada(s)']);
    }

    public function getAll()
    {
        $programmed_invoices = ProgramedInvoice::latest()
            ->with('companyBranch:id,name')
            ->where('status', 'Pendiente')
            ->paginate(30);

        return response()->json(compact('programmed_invoices'));
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $programmed_invoices = ProgramedInvoice::latest()->with('companyBranch:id,name')
            ->where('id', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhere('sale_id', 'like', "%{$query}%")
            ->where('status', 'Pendiente')
            ->get();

        return response()->json(['items' => $programmed_invoices], 200);
    }
}
