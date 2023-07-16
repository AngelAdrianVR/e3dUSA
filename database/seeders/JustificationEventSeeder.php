<?php

namespace Database\Seeders;

use App\Models\JustificationEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JustificationEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incidents = (array(
            [
                'name' => 'Permiso sin goce',
                'description' => 'No se paga el día pero no afecta los bonos',
                'additionals' => json_encode(["color" => "yellow"]),
            ],
            [
                'name' => 'Permiso con goce',
                'description' => 'Se paga el 100% del día',
                'additionals' => json_encode(["color" => "yellow"]),
            ],
            [
                'name' => 'Vacaciones',
                'description' => 'Se paga el 100% del día y 25% extra por la prima vacacional',
                'additionals' => json_encode(["color" => "yellow"]),
            ],
            [
                'name' => 'Falta justificada',
                'description' => 'No se paga el día pero no afecta los bonos. Es requerido algún comprobante que justifique la falta',
                'additionals' => json_encode(["color" => "red"]),
            ],
            [
                'name' => 'Falta injustificada',
                'description' => 'No se paga el día y afecta a los bonos',
                'additionals' => json_encode(["color" => "red"]),
            ],
            [
                'name' => 'Descanso',
                'description' => 'Marcar el día como descanso',
                'additionals' => json_encode(["color" => "green"]),
            ],
            [
                'name' => 'Sin registro',
                'description' => 'No ha pasado este día',
                'additionals' => json_encode(["color" => "transparent"]),
            ],
        ));

        foreach ($incidents as $incident) {
            JustificationEvent::create($incident);
        }
    }
}
