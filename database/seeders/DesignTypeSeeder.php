<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('design_types')->insert(array(
            [
                'name' => 'Render',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Plano',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Positivo',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => '2D',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Recorte de vinil',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Corte láser',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Escantillón',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Archivo para impresión en cama plana',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Archivo para grabado láser',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Proyecto especial',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            [
                'name' => 'Formato para autorización',
                'min_time' => 60,
                'max_time' => 180,
                'created_at' => now(),
            ],
            
        ));
    }
}
