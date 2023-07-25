<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function RawMaterialActualStock()
    {
        $data = Storage::with('storageable')->where('type', 'materia-prima')->latest()->get();
        $date = today()->isoFormat('DD-MM-YYYY');
        $pdf = Pdf::loadView('PDF.raw-materials-actual-stock', compact('data'));

        return $pdf->download("reporte_materia_prima_$date.pdf");
    }

    public function consumablesActualStock ()
    {
        $data = Storage::with('storageable')->where('type', 'consumible')->latest()->get();
        $date = today()->isoFormat('DD-MM-YYYY');
        $pdf = Pdf::loadView('PDF.consumable-actual-stock', compact('data'));

        return $pdf->download("reporte_insumos_$date.pdf");
    }
}
