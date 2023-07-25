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
                'name' => 'Serigrafía manual a una tinta',
                'description' => 'serigrafía a mano, una sola tinta',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía manual a dos tintas',
                'description' => 'serigrafía a mano, dos tintas',
                'cost' => 2,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía manual a tres tintas',
                'description' => 'serigrafía a mano, tres tintas',
                'cost' => 3,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía semiautomatico una tinta',
                'description' => 'serigrafía en máquina semiautomatica, una tinta',
                'cost' => 0.5,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía semiautomatico dos tintas',
                'description' => 'serigrafía en máquina semiautomatica, dos tintas',
                'cost' => 0.5,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía automatica una tinta',
                'description' => 'serigrafía en pulpo automatico, una tinta',
                'cost' => 0.1,
                'created_at' => now(),
            ],
            [
                'name' => 'Serigrafía automatica dos tintas',
                'description' => 'serigrafía en pulpo automatico, dos tintas',
                'cost' => 0.1,
                'created_at' => now(),
            ],
            [
                'name' => 'Grabado láser',
                'description' => 'Grabado láser en producto',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Ensamblaje de llavero',
                'description' => 'Ensamblaje de medallón y/o argolla en llavero',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Pegar emblema',
                'description' => 'Pegar emblema a superficie',
                'cost' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Pegar vinil',
                'description' => 'Pegar vinil a superficie',
                'cost' => 1.5,
                'created_at' => now(),
            ],
        ));
    }
}
