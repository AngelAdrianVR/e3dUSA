<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('holidays')->insert(array(
            [
                'name' => 'Día de la Constitución Mexicana',
                'date' => '2023-02-06',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Natalicio de Benito Juárez',
                'date' => '2023-03-20',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Día Internacional de los Trabajadores',
                'date' => '2023-05-01',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Día de la Independencia de México',
                'date' => '2023-09-16',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Aniversario de la Revolución Mexicana',
                'date' => '2023-11-20',
                'is_active' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Navidad',
                'date' => '2023-12-25',
                'is_active' => 1,
                'created_at' => now(),
            ],
            // no oficial days
            [
                'name' => 'Jueves Santo',
                'date' => '2023-04-06',
                'is_active' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'Viernes Santo',
                'date' => '2023-04-07',
                'is_active' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'Batalla de Puebla',
                'date' => '2023-05-05',
                'is_active' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'Día de la Raza',
                'date' => '2023-10-12',
                'is_active' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'Día de Muertos',
                'date' => '2023-11-02',
                'is_active' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'Día de la Virgen de Guadalupe',
                'date' => '2023-12-12',
                'is_active' => 0,
                'created_at' => now(),
            ],

        ));
    }
}
