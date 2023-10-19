<?php

namespace App\Console\Commands;

use App\Models\RawMaterial;
use App\Models\User;
use App\Notifications\StockRepositionNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockReposition extends Command
{
    protected $signature = 'app:stock-reposition';
    protected $description = 'check if a raw materia has equal or less than min limit to reposition';

    public function handle()
    {
        $rawMaterials = RawMaterial::with('storages')->whereHas('storages', function ($query) {
            // Filtrar por almacenes que tengan cantidad igual o menor al límite (min_quantity)
            $query->where('quantity', '<=', DB::raw('raw_materials.min_quantity'));
        })->get();

        if ($rawMaterials->count()) {
            $super_admins = User::whereNull('employee_properties')->get();
            $others = User::whereIn('employee_properties->department', ['Administración', 'Compras', 'Almacén'])->where('is_active', 1)->get();

            // notify users
            foreach ($others as $other) {
                $other->notify(new StockRepositionNotification($rawMaterials));
            }
            foreach ($super_admins as $super) {
                $super->notify(new StockRepositionNotification($rawMaterials));
            }
            Log::info('app:stock-reposition executed successfully. Low stock products:' . $rawMaterials->count());
        } else {
            Log::info('app:stock-reposition executed successfully. Low stock products:' . $rawMaterials->count());
        }

    }
}
