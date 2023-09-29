<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PdfController extends Controller
{
    public function RawMaterialActualStock()
    {
        $data = Storage::with('storageable')->where('type', 'materia-prima')->latest()->get();       
        // $date = today()->isoFormat('DD-MM-YYYY');
        // $pdf = Pdf::loadView('PDF.raw-materials-actual-stock', compact('data'));

        // return $pdf->download("reporte_materia_prima_$date.pdf");

        return inertia('Storage/Pdf/RawMaterialCurrentStock', compact('data'));
    }

    public function consumablesActualStock()
    {
        $data = Storage::with('storageable')->where('type', 'consumible')->latest()->get();
        // $date = today()->isoFormat('DD-MM-YYYY');
        // $pdf = Pdf::loadView('PDF.consumable-actual-stock', compact('data'));

        // return $pdf->download("reporte_insumos_$date.pdf");

        return inertia('Storage/Pdf/ConsumableCurrentStock', compact('data'));
    }

    public function RawMaterialInfo()
    {
        $data = Storage::with(['storageable' => function ($query) {
            $query->select('id', 'name', 'part_number');
        }, 'storageable.media'])->where('type', 'materia-prima')->whereHas('storageable', function ($query) {
            $query->where('part_number', 'LIKE', 'OV%');
        })->get();

        $date = today()->isoFormat('DD-MM-YYYY');
        $pdf = Pdf::loadView('PDF.raw-material-info', compact('data'));

        return $pdf->download("reporte_productos_$date.pdf");
    }
}
