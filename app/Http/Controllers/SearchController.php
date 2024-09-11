<?php

namespace App\Http\Controllers;

use App\Models\CatalogProduct;
use App\Models\CompanyBranch;
use App\Models\Design;
use App\Models\Machine;
use App\Models\Oportunity;
use App\Models\Prospect;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\Sample;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $numOfitems = 45;
        $query = $request->input('query');

        // Búsqueda en el modelo User  -------------------
        $users = User::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'model' => 'users',
            ];
        });
        // -----------------------------------------------

        // Búsqueda en el modelo catalogProduct ----------------------------
        $catalog_products = CatalogProduct::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $catalog_products = $catalog_products->map(function ($catalog_product) {
            return [
                'id' => $catalog_product->id,
                'name' => $catalog_product->name,
                'model' => 'catalog-products',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo raw-materials
        $raw_materials = RawMaterial::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $raw_materials = $raw_materials->map(function ($raw_material) {
            return [
                'id' => $raw_material->id,
                'name' => $raw_material->name,
                'model' => 'storages',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo company branch
        $company_branches = CompanyBranch::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name', 'company_id'])
            ->take($numOfitems);

        $company_branches = $company_branches->map(function ($company_branch) {
            return [
                'id' => $company_branch->company_id,
                'name' => $company_branch->name,
                'model' => 'companies',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo design
        $designs = Design::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $designs = $designs->map(function ($design) {
            return [
                'id' => $design->id,
                'name' => $design->name,
                'model' => 'designs',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo machine
        $machines = Machine::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $machines = $machines->map(function ($machine) {
            return [
                'id' => $machine->id,
                'name' => $machine->name,
                'model' => 'machines',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo opportunit
        $opportunities = Oportunity::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $opportunities = $opportunities->map(function ($opportunity) {
            return [
                'id' => $opportunity->id,
                'name' => $opportunity->name,
                'model' => 'oportunities',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo prospect
        $prospects = Prospect::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $prospects = $prospects->map(function ($prospect) {
            return [
                'id' => $prospect->id,
                'name' => $prospect->name,
                'model' => 'prospects',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo sample
        $samples = Sample::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $samples = $samples->map(function ($sample) {
            return [
                'id' => $sample->id,
                'name' => $sample->name,
                'model' => 'samples',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo sparePart
        $spareParts = SparePart::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name', 'machine_id'])
            ->take($numOfitems);

        $spareParts = $spareParts->map(function ($sparePart) {
            return [
                'id' => $sparePart->machine_id,
                'name' => $sparePart->name,
                'model' => 'machines',
            ];
        });
        // ---------------------------------------------------------------

        // Búsqueda en el modelo supplier
        $suppliers = Supplier::where('name', 'like', "%$query%")
            ->orWhere('id', 'like', "$query%")
            ->latest('id')
            ->get(['id', 'name'])
            ->take($numOfitems);

        $suppliers = $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'model' => 'suppliers',
            ];
        });
        // ---------------------------------------------------------------

        // Combina los resultados en un array
        $results = [
            'Usuarios' => $users,
            'Productos de catalogo' => $catalog_products,
            'Materia prima' => $raw_materials,
            'Sucursales' => $company_branches,
            'Diseños' => $designs,
            'Máquinas' => $machines,
            'Oportunidades' => $opportunities,
            'Prospectos' => $prospects,
            'Muestras' => $samples,
            'Refacciones' => $spareParts,
            'Proveedores' => $suppliers,
        ];

        return response()->json(['results' => $results]);
    }
}
