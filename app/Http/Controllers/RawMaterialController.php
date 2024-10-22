<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\RawMaterialResource;
use App\Models\CatalogProduct;
use App\Models\RawMaterial;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RawMaterialController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
        $last = RawMaterial::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;

        if (Route::currentRouteName() == 'raw-materials.create') {
            return inertia('Storage/Create/RawMaterial');
        } elseif (Route::currentRouteName() == 'consumables.create') {
            return inertia('Storage/Create/Consumable');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            'part_number' => 'required',
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
        ]);

        // consecutive
        $last = RawMaterial::latest()->first();
        $next_id = $last ? $last->id + 1 : 1;
        $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
        $validated['part_number'] .= $consecutive;

        $raw_material = RawMaterial::create($validated);
        $raw_material->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        $raw_material->storages()->create([
            'quantity' => $request->initial_stock,
            'type' => $request->type,
            'location' => $request->location,
        ]);

        event(new RecordCreated($raw_material));

        if ($request->is_catalog_product) {
            // crear producto de catalogo
            // consecutive
            $last = CatalogProduct::latest()->first();
            $next_id = $last ? $last->id + 1 : 1;
            $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
            $exploded = explode('-', $validated['part_number']);
            $partNumbe = "C-{$exploded[0]}-{$exploded[1]}-" . $consecutive;
            $data = [
                'name' => $request->name,
                'part_number' => $partNumbe,
                'measure_unit' => $request->measure_unit,
                'min_quantity' => $request->min_quantity,
                'max_quantity' => $request->max_quantity,
                'description' => $request->description,
                'cost' => $request->cost,
            ];
            $catalog_product = CatalogProduct::create($data);
            $rawMaterialData = [
                'quantity' => 1,
                'production_costs' => [15],
            ];
            $catalog_product->rawMaterials()->attach($raw_material->id, $rawMaterialData);

            $media = $raw_material->getFirstMedia();
            if ($media) {
                $catalog_product_media = $catalog_product->addMedia($media->getPath())
                    ->usingFileName($media->file_name)
                    ->toMediaCollection();

                // Obtén la ruta de almacenamiento del RawMaterial
                $raw_material_storage_path = $raw_material->getFirstMediaPath();

                // Copia la imagen al almacenamiento del RawMaterial porque se elimina al guardar en catalog product
                File::copy($catalog_product_media->getPath(), $raw_material_storage_path);
            }

            // crear existencias en almacen de producto terminado
            $catalog_product->storages()->create([
                'quantity' => $request->initial_stock,
                'location' => $request->location,
                'type' => 'producto-terminado',
            ]);
        }

        if ($request->type == 'materia-prima')
            return to_route('storages.raw-materials.index');
        else
            return to_route('storages.consumables.index');
    }

    public function show(RawMaterial $rawMaterial)
    {
        //
    }

    public function edit($raw_material)
    {
        $raw_material = RawMaterialResource::make(RawMaterial::with('storages')->find($raw_material));
        $media = $raw_material->getMedia();

        return inertia('Storage/Edit/RawMaterial', compact('raw_material', 'media'));
    }

    public function editConsumable($raw_material)
    {
        $raw_material = RawMaterialResource::make(RawMaterial::with('storages')->find($raw_material));

        return inertia('Storage/Edit/Consumable', compact('raw_material'));
    }

    public function update(Request $request, RawMaterial $raw_material)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'material' => 'required|string',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            // 'part_number' => 'required|string', Se eliminaba el co nsecutivo, no descomentar
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'location' => 'required|string',
        ]);


        $raw_material->update($validated);
        $raw_material->storages()->update([
            'quantity' => $request->initial_stock,
            'location' => $request->location,
        ]);

        event(new RecordEdited($raw_material));
        // 

        if ($request->type == 'materia-prima')
            return to_route('storages.raw-materials.index');
        else
            return to_route('storages.consumables.index');
    }

    public function updateWithMedia(Request $request, RawMaterial $raw_material)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'material' => 'required|string',
            'width' => 'required|numeric|min:0|max:2000',
            'large' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'height' => $request->is_circular ? 'nullable' : 'required|numeric|min:0|max:2000',
            'diameter' => $request->is_circular ? 'required|numeric|min:0|max:2000' : 'nullable',
            // 'part_number' => 'required|string', Se eliminaba el consecutivo, no descomentar
            'measure_unit' => 'required',
            'min_quantity' => 'required|numeric|min:0',
            'max_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'location' => 'required|string',
        ]);


        $raw_material->update($validated);
        $raw_material->storages()->update([
            'quantity' => $request->initial_stock,
            'location' => $request->location,
        ]);

        // update image
        $raw_material->clearMediaCollection();
        $raw_material->addMediaFromRequest('media')->toMediaCollection();

        event(new RecordEdited($raw_material));

        if ($request->type == 'materia-prima')
            return to_route('storages.raw-materials.index');
        else
            return to_route('storages.consumables.index');
    }


    public function destroy(RawMaterial $raw_material)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->raw_materials as $raw_material) {
            $raw_material = Storage::find($raw_material['id']);
            $raw_material?->delete();
            $raw_material = RawMaterial::find($raw_material['storageable_id']);
            $raw_material?->delete();

            event(new RecordDeleted($raw_material));
        }

        return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function turnIntoCatalogProduct(Request $request)
    {
        $rawMaterial = RawMaterial::findOrFail($request->raw_material_id);

        // Verificar si ya está en el catálogo
        if (!$rawMaterial->isInCatalogProduct()) {
            // part number
            $last = CatalogProduct::latest()->first();
            $next_id = $last ? $last->id + 1 : 1;
            $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
            $family = explode('-', $rawMaterial->part_number)[0];
            $part_number = "C-$family-GEN-$consecutive";

            // Crear un nuevo producto de catálogo
            $catalogProduct = CatalogProduct::create([
                'name' => $rawMaterial->name,
                'description' => $rawMaterial->description,
                'part_number' => $part_number,
                'measure_unit' => $rawMaterial->measure_unit,
                'cost' => $rawMaterial->cost,
                'min_quantity' => $rawMaterial->min_quantity,
                'max_quantity' => $rawMaterial->max_quantity,
                'features' => $rawMaterial->features,
            ]);

            // Clonar la imagen si existe
            $rawMaterialImage = $rawMaterial->getFirstMedia();

            if ($rawMaterialImage && file_exists($rawMaterialImage->getPath())) {
                // Crear una nueva instancia de Media
                $clonedImage = $catalogProduct
                    ->addMedia($rawMaterialImage->getPath())
                    ->preservingOriginal()
                    ->toMediaCollection();

                // Agregar la imagen clonada al producto de catálogo
                $catalogProduct->media()->save($clonedImage);
            }

            // Agregar el material al producto de catálogo
            $catalogProduct->rawMaterials()->attach($rawMaterial, [
                'quantity' => 1,
                'production_costs' => [15],
            ]);
            return response()->json(['message' => 'Producto agregado al catálogo con éxito.', 'type' => 'success', 'title' => 'Éxito']);
        }

        return response()->json(['message' => 'Este producto ya existe en el catalogo', 'type' => 'info', 'title' => '']);
    }

    public function fetchItem($raw_material_id)
    {
        $raw_material = RawMaterial::with('media')->find($raw_material_id);

        return response()->json(['item' => $raw_material]);
    }


    public function fetchSupplierItems($raw_materials_ids)
    {
        $raw_materials = [];
        $ids_array = explode(',', $raw_materials_ids);

        foreach ($ids_array as $raw_material_id) {
            $raw_material = RawMaterial::with('media', 'storages')->find($raw_material_id);

            if ($raw_material) {
                $raw_materials[] = $raw_material;
            }
        }

        return response()->json(['items' => $raw_materials]);
    }
}
