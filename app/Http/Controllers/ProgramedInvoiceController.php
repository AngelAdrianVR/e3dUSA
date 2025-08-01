<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Calendar;
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
        $user = auth()->user();
        $programmed_invoice->update($request->all());
        
        // Revisa si existe en el calendario ese recordatorio
        $invoice_calendar = Calendar::where('user_id', $programmed_invoice->user_id)
            ->where('title', 'like', 'Factura programada para OV-' . $programmed_invoice->sale_id . '%')
            ->first();

        // Edita la fecha y la hora del recordatorio en el calendario si es que existe
        if ( $invoice_calendar ) {
            $invoice_calendar->update([
                'start_date' => $programmed_invoice->reminder_date,
                'start_time' => $programmed_invoice->reminder_time,
            ]);
        }

        // Revisa si ya no hay facturas pendientes por hacer para quitar el recordatorio invasivo de pantalla
        $today = now()->startOfDay();

        $reminder = ProgramedInvoice::where('status', 'Pendiente')
            ->where('user_id', $user->id)
            ->whereDate('reminder_date', $today)
            ->first();
        
        if ( !$reminder ) {
            $user->update(['programmed_invoice_reminder' => false]);
        } else {
            $user->update(['programmed_invoice_reminder' => true]);
        }
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
            ->paginate(150);

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
