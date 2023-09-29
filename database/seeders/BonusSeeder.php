<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bonuses')->insert(array(
            [
                'name' => 'Asistencia',
                'description' => '...',
                'half_time' => 117,
                'full_time' => 170,
                'created_at' => now(),
            ],
            [
                'name' => 'Puntualidad',
                'description' => '...',
                'half_time' => 117,
                'full_time' => 170,
                'created_at' => now(),
            ],
            [
                'name' => 'Productividad',
                'description' => '...',
                'half_time' => 100,
                'full_time' => 100,
                'created_at' => now(),
            ],
            [
                'name' => 'Prima dominical',
                'description' => '...',
                'half_time' => 52.76,
                'full_time' => 52.76,
                'created_at' => now(),
            ],
            [
                'name' => 'Puntualidad jefe de producción',
                'description' => '...',
                'half_time' => 50,
                'full_time' => 50,
                'created_at' => now(),
            ],
        ));
    }
}
