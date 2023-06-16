<?php

namespace Database\Seeders;

use App\Models\ProductionCost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionCostSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('production_costs')->insert(array(
            [
                'name' => 'Serigrafía manual',
                'description' => 'serigrafía a mano 1 sola tinta',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía semiautomatico',
                'description' => 'serigrafía en maquina semiautomatica',
                'cost' => 0.5,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía automatica',
                'description' => 'serigrafía en pulpo automatico',
                'cost' => 0.1,
                'created_at' => now(),
            ],
            [
                'name' => 'Grabado laser',
                'description' => 'Grabado laser en producto',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Endsamblaje de llavero',
                'description' => 'Ensamblaje de medallón y argolla en llavero',
                'cost' => 2,
                'created_at' => now(),
            ],
        ));
    }
}
