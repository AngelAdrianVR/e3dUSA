<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\CatalogProductResource;
use App\Models\Brand;
use App\Models\CatalogProduct;
use App\Models\ProductionCost;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use App\Exports\CatalogProductPricesExport;
use Maatwebsite\Excel\Facades\Excel;

class CatalogProductController extends Controller
{
    public function index()
    {
        $catalog_products = CatalogProduct::with('media')
            ->latest()
            ->select(['id', 'part_number', 'name', 'cost', 'description'])
            ->paginate(30);

        // return $catalog_products;
        return inertia('CatalogProduct/Index', compact('catalog_products'));
    }

    public function create()
    {
        $raw_materials = RawMaterial::all(['id', 'name']);
        $production_costs = ProductionCost::all(['id', 'name', 'cost']);
        $brands = Brand::all();

        // consecutive
        $last = CatalogProduct::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);

        return inertia('CatalogProduct/Create', compact('raw_materials', 'production_costs', 'consecutive', 'brands'));
    }

    public function store(Request $request)
    {
        // total production cost
        $total_cost = 0;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'part_number' => 'required|string|unique:catalog_products,part_number',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            'raw_materials.*.production_cost' => 'array|min:1',
            'features' => 'nullable',
        ]);

        // consecutive
        $last = CatalogProduct::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
        $validated['part_number'] .= $consecutive;
       
        $catalog_product = CatalogProduct::create($validated);

        // Subir y asociar las imagenes
        $catalog_product->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            // costo de produccion
            $costs = $product['production_costs'] ?? [];
            foreach ($costs as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordCreated($catalog_product));

        // return to_route('catalog-products.index');
        return to_route('catalog-products.show', $catalog_product->id);
    }

    public function show($catalog_product_id)
    {
        $catalog_product = CatalogProductResource::make(CatalogProduct::with(['rawMaterials:id,name,part_number' 
            => ['storages:id,storageable_type,storageable_id' => ['storageable:id,name']], 'storages', 'companies:id,business_name'])->find($catalog_product_id));
        $catalog_products = CatalogProduct::latest()->get(['id', 'name']);
        
        return inertia('CatalogProduct/Show', compact('catalog_products', 'catalog_product'));
    }

    public function edit(CatalogProduct $catalog_product)
    {
        $catalog_product = CatalogProduct::with('rawMaterials')->find($catalog_product->id);
        $production_costs = ProductionCost::all();
        $raw_materials = RawMaterial::all(['id', 'name']);
        $brands = Brand::all();
        $media = $catalog_product->getFirstMedia();

        return inertia('CatalogProduct/Edit', compact('catalog_product', 'production_costs', 'raw_materials', 'media', 'brands'));
    }

    public function update(Request $request, CatalogProduct $catalog_product)
    {
        $total_cost = 0;
        $request->validate([
            'name' => 'required|string|max:254',
            'brand' => 'required|string|max:255',
            'part_number' => 'required|string',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            'raw_materials.*.production_cost' => 'array|min:1',
        ]);

        $catalog_product->update($request->all());
        $catalog_product->rawMaterials()->detach();

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            // costo de produccion
            $costs = $product['production_costs'] ?? [];
            foreach ($costs as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordEdited($catalog_product));

        // return to_route('catalog-products.index');
        return to_route('catalog-products.show', $catalog_product->id);
    }

    public function updateWithMedia(Request $request, CatalogProduct $catalog_product)
    {
        $total_cost = 0;
        $request->validate([
            'name' => 'required|string|max:254',
            'brand' => 'required|string|max:255',
            'part_number' => 'required|string',
            'measure_unit' => 'required|string',
            'min_quantity' => 'required|min:0',
            'max_quantity' => 'required|min:0',
            'description' => 'nullable',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            'raw_materials.*.production_cost' => 'array|min:1'
        ]);

        $catalog_product->update($request->all());
        $catalog_product->rawMaterials()->detach();

        foreach ($request->raw_materials as $product) {
            $total_cost += RawMaterial::find($product['raw_material_id'])?->cost * $product['quantity'];

            foreach ($product['production_costs'] as $process_id) {
                $total_cost += ProductionCost::find($process_id)->cost * $product['quantity'];
            }

            $catalog_product->rawMaterials()->attach($product['raw_material_id'], $product);
        }

        // update images. Clear all then attach all
        $catalog_product->clearMediaCollection();
        $catalog_product->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        $catalog_product->update(['cost' => $total_cost]);

        event(new RecordEdited($catalog_product));

        // return to_route('catalog-products.index');
        return to_route('catalog-products.show', $catalog_product->id);
    }

    public function destroy(CatalogProduct $catalog_product)
    {
        $catalog_product_name = $catalog_product->name;
        $catalog_product->delete();

        event(new RecordDeleted($catalog_product));

        return response()->json(['message' => "Producto eliminado: $catalog_product_name"]);
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->catalog_products as $catalog_product) {
            $catalog_product = CatalogProduct::find($catalog_product['id']);
            $catalog_product?->delete();
            
            event(new RecordDeleted($catalog_product));
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function clone(Request $request)
    {
        $catalog_product = CatalogProduct::find($request->catalog_product_id);

        $clone = $catalog_product->replicate()->fill([
            'authorized_user_name' => null,
            'authorized_at' => null,
            'name' => $catalog_product->name . ' (Copia)',
            'part_number' => $catalog_product->part_number . '-Copia',
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();

        foreach ($catalog_product->rawMaterials as $product) {
            $pivot = [
                'quantity' => $product->pivot->quantity,
                'production_costs' => $product->pivot->production_costs,
            ];

            $clone->rawMaterials()->attach($product->pivot->raw_material_id, $pivot);
        }

        // cloning files
        $original_files = $catalog_product->getMedia();

        foreach ($original_files as $file) {
            $copy_file = $file->copy($clone); // Copy file to cloned item
        }

        $clone->save();

        return response()->json(['message' => "Producto clonado: {$clone->part_number}", 'newItem' => catalogProductResource::make(CatalogProduct::with('storages')->find($clone->id))]);
    }

    public function QRSearchCatalogProduct(Request $request)
    {

        $request->validate([ 
            'barCode' => 'required|string'
        ]);

        $part_number = explode('#', $request->barCode)[0];

        // Verifica si el formato es incorrecto (contiene "'") y realiza el reemplazo solo en ese caso
        if (strpos($part_number, "'") !== false) {
            $part_number = str_replace("'", "-", $part_number);
        }

        if (strpos($part_number, "´") !== false) {
            $part_number = str_replace("´", "-", $part_number);
        }

        $catalog_product = CatalogProductResource::make(CatalogProduct::with(['storages', 'companies'])->where('part_number', $part_number)->first());

        $companies = $catalog_product->companies;

        return response()->json(['item' => $catalog_product]);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $catalog_products = CatalogProduct::with('media')
            ->latest()
            ->select(['id', 'part_number', 'name', 'cost', 'description'])
            ->where('id', 'like', "%{$query}%")
            ->orWhere('part_number', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->get(['id', 'folio', 'created_at', 'created_by', 'invoice_quantity', 'company_branch_id', 'invoice_amount', 'complements', 'sale_id', 'status', 'number_of_invoice']);

        return response()->json(['items' => $catalog_products], 200);
    }

    public function getCatalogProductData(Request $request, CatalogProduct $catalog_product)
    {
        $catalog_product = $catalog_product->load(['media']);
        $stock = $catalog_product->storages->count() ? $catalog_product->storages[0] : null;
        $raw_materials = $catalog_product->rawMaterials;
        $commited_units = [];
        foreach ($raw_materials as $item) {
            $commited_units[] = $item->getCommitedUnits();
        }

        return response()->json(['item' => $catalog_product, 'stock' => $stock, 'commited_units' => $commited_units]);
    }
    
    public function fetchShippingRates(CatalogProduct $catalog_product)
    {
        $catalog_product->load('shippingRates:id,catalog_product_id');

        return response()->json(['item' => $catalog_product]);
    }
    
    public function pricesReport()
    {
        $catalog_products = CatalogProduct::with(['companies', 'media'])
            ->orderBy('name')
            ->get(['id', 'part_number', 'name', 'cost']);

        return inertia('CatalogProduct/PricesReport', compact('catalog_products'));
    }

    public function getByIds(Request $request)
    {
        $ids_array = $request->ids;

        $items = CatalogProduct::with('media')->whereIn('id', $request->ids)->get();

        return response()->json(compact('items'));
    }

    public function getInfo(CatalogProduct $catalog_product)
    {
        $catalog_product = $catalog_product->load(['media']);

        return response()->json(['item' => $catalog_product]);
    }

    public function exportExcel()
    {
        $fileName = 'catalogo_precios.xlsx';
        
        return Excel::download(new CatalogProductPricesExport, $fileName);
    }
}
