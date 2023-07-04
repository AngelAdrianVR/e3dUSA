<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class justificationEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('justification_events')->insert(array(
            [
                'name' => 'Permiso Sin Goce',
                'description' => 'No se paga el día pero no afecta los bonos',
                'additionals' => ['color' => 'yellow']
                'created_at' => now(),
            ],
            [
                'name' => 'Permiso con Goce',
                'description' => 'Se paga el 100% del día',
                'additionals' => ['color' => 'yellow']
                'created_at' => now(),
            ],
            [
                'name' => 'Vacaciones',
                'description' => 'Se paga el 100% del día y 25% extra por la prima vacacional',
                'additionals' => ['color' => 'yellow']
                'created_at' => now(),
            ],
            [
                'name' => 'Falta justificada',
                'description' => 'No se paga el día pero no afecta los bonos. Es requerido algún comprobante que justifique la falta',
                'additionals' => ['color' => 'red']
                'created_at' => now(),
            ],
            [
                'name' => 'Falta injustificada',
                'description' => 'No se paga el día y afecta a los bonos',
                'additionals' => ['color' => 'red']
                'created_at' => now(),
            ],
            [
                'name' => 'Descanso',
                'description' => 'Marcar el día como descanso',
                'additionals' => ['color' => 'green']
                'created_at' => now(),
            ],
        ));
    }
}
