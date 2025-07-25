<?php

namespace App\Http\Controllers;

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

    public function show(ProgramedInvoice $programedInvoice)
    {
        //
    }

    public function edit(ProgramedInvoice $programedInvoice)
    {
        //
    }

    public function update(Request $request, ProgramedInvoice $programedInvoice)
    {
        //
    }

    public function destroy(ProgramedInvoice $programedInvoice)
    {
        //
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
            ->get();

        return response()->json(['items' => $programmed_invoices], 200);
    }
}
