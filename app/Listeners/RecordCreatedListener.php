<?php

namespace App\Listeners;

use App\Events\RecordCreated;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordCreatedListener
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
    public function handle(RecordCreated $event): void
    {
         // Accede al registro creado desde el evento
         $recordCreated = $event->record;

         // Obtén la información del usuario actual (puedes usar el sistema de autenticación de Laravel)
         $user = auth()->user();

         // Obtén el nombre de la tabla del modelo del registro creado
        $tableName = $recordCreated->getTable();

 
         // Crea un nuevo registro de auditoría
         Audit::create([
             'user_id' => $user->id,
             'action' => 'Creación', 
             'table_name' =>  $tableName, 
             'record_id' => $recordCreated->id,
            //  'old_data' => null,
            //  'new_data' => $recordCreated, // Suponiendo que $registroCreado es un array o objeto con los datos del registro
         ]);
    }
}
