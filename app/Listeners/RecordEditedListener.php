<?php

namespace App\Listeners;

use App\Events\RecordEdited;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordEditedListener
{
    
    public function __construct()
    {
        //
    }

    
    public function handle(RecordEdited $event): void
    {
        // Accede al registro creado desde el evento
        $recordEdited = $event->record;

        // Obtén la información del usuario actual (puedes usar el sistema de autenticación de Laravel)
        $user = auth()->user();

        // Obtén el nombre de la tabla del modelo del registro creado
       $tableName = $recordEdited->getTable();


        // Crea un nuevo registro de auditoría
        Audit::create([
            'user_id' => $user->id,
            'action' => 'Edición', 
            'table_name' =>  $tableName, 
            'record_id' => $recordEdited->id,
            // 'old_data' => $recordEdited->getOriginal(),
            'new_data' => $recordEdited, 
        ]);
    }
}
