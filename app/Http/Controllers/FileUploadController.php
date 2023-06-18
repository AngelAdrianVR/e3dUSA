<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            return $file;
            // Aquí puedes realizar la lógica de procesamiento del archivo
            // Por ejemplo, puedes moverlo a una ubicación específica, almacenar información en la base de datos, etc.

            return response()->json(['message' => 'Archivo cargado correctamente']);
        }

        return response()->json(['message' => 'No se ha proporcionado ningún archivo'], 400);
    }
}
