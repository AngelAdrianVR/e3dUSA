<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Supplier;
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
            $direction = User::whereIn('employee_properties->puesto', ['Asistente de director'])->where('is_active', 1)->get();

            $suppliers = $this->getSuppliersWithRawMaterials($rawMaterials);
            $purchasesData = $this->createSuppliersWithRawMaterialsList($suppliers, $rawMaterials);

            // crear ordenes de compra por proveedor
            foreach ($purchasesData as $data) {
                Purchase::create([
                    'user_id' => 1, //super admin
                    'supplier_id' => $data['supplier_id'],
                    'products' => $data['raw_materials'],
                ]);
            }

            // // notify users
            // foreach ($others as $other) {
            //     $other->notify(new StockRepositionNotification($rawMaterials));
            // }
            // foreach ($direction as $item) {
            //     $item->notify(new StockRepositionNotification($rawMaterials));
            // }
            // foreach ($super_admins as $super) {
            //     $super->notify(new StockRepositionNotification($rawMaterials));
            // }
            // Log::info('app:stock-reposition executed successfully. Low stock products:' . $rawMaterials->count());
        } else {
            Log::info('app:stock-reposition executed successfully. Low stock products: 0');
        }
    }

    public function getSuppliersWithRawMaterials($lowStockRawMaterials)
    {
        // Obtener todos los proveedores
        $suppliers = Supplier::all();

        // Filtrar los proveedores que tienen al menos una materia prima en común
        $filtered = $suppliers->filter(function ($supplier) use ($lowStockRawMaterials) {
            return count(array_intersect($supplier->raw_materials_id ?? [], $lowStockRawMaterials->pluck('id')->toArray())) > 0;
        });

        return $filtered;
    }

    public function createSuppliersWithRawMaterialsList($suppliers, $lowStockRawMaterials)
    {
        $suppliersList = [];

        foreach ($suppliers as $supplier) {
            $rawMaterials = array_intersect($supplier->raw_materials_id, $lowStockRawMaterials->pluck('id')->toArray());

            // Filtrar los elementos que coinciden con los IDs del array
            $filteredRawMaterials = $lowStockRawMaterials->filter(function ($rawMaterial) use ($rawMaterials) {
                return in_array($rawMaterial->id, $rawMaterials);
            });

            // Mapear las propiedades deseadas de cada elemento en el array filtrado
            $processedArray = $filteredRawMaterials->map(function ($rawMaterial) {
                return [
                    'id' => $rawMaterial->id,
                    'name' => $rawMaterial->name,
                    'quantity' => $rawMaterial->max_quantity - $rawMaterial->storages[0]->quantity,
                ];
            });

            $suppliersList[] = [
                'supplier_id' => $supplier->id,
                'raw_materials' => $processedArray->values(),
            ];
        }

        return $suppliersList;
    }
}
