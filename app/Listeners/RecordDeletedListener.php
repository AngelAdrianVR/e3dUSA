<?php

namespace App\Listeners;

use App\Events\RecordDeleted;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordDeletedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecordDeleted $event): void
    {
         // Accede al registro creado desde el evento
         $recordDeleted = $event->record;

         // Obtén la información del usuario actual (puedes usar el sistema de autenticación de Laravel)
         $user = auth()->user();

         // Obtén el nombre de la tabla del modelo del registro creado
        $tableName = $recordDeleted->getTable();

 
         // Crea un nuevo registro de auditoría
         Audit::create([
             'user_id' => $user->id,
             'action' => 'Eliminación', 
             'table_name' =>  $tableName, 
             'record_id' => $recordDeleted->id,
            //  'old_data' => null,
            //  'new_data' => $recordDeleted, // Suponiendo que $registroCreado es un array o objeto con los datos del registro
         ]);
    }
}
