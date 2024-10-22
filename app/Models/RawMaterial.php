<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RawMaterial extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'brand',
        'material',
        'width',
        'large',
        'height',
        'diameter',
        'part_number',
        'description',
        'measure_unit',
        'min_quantity',
        'max_quantity',
        'cost',
        'min_quantity_purchase', //para mostrar en proveedores
        'notes', //para mostrar en proveedores
        'features',
    ];

    protected $casts = [
        'features' => 'array'
    ];

    // relationships---------------------------------------

    /**
     * Get the RawMaterial's sorage in warehose.
     */
    public function storages(): MorphMany
    {
        return $this->morphMany(Storage::class, 'storageable');
    }

    public function catalogProducts(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class)->using(CatalogProductRawMaterial::class)
            ->withPivot([
                'quantity',
            ])->withTimestamps();
    }

    public function quotes(): BelongsToMany
    {
        return $this->belongsToMany(Quote::class)
            ->withPivot([
                'quantity',
                'price',
                'show_image',
                'notes',
            ])->withTimestamps();
    }

    // methods----------------------------------------------
    public function isInCatalogProduct()
    {
        // Obtener todos los productos de catálogo en los que está esta materia prima
        $catalogProducts = $this->catalogProducts;

        if ($catalogProducts->isEmpty()) {
            return false; // No está en ningún producto de catálogo
        }

        // Verificar en cada producto de catálogo si esta es la única materia prima
        foreach ($catalogProducts as $catalogProduct) {
            if ($catalogProduct->rawMaterials()->count() == 1) {
                return true; // ya hay un producto de catalogo con solo esta materia prima como componente
            }
        }

        return false; // No hay producto de catalogo con solo este componente
    }

    public function getSalesCommited()
    {
        // buscar las OV los cuales algun producto ordenado contiene esta materia prima
        $sales = Sale::whereNotNull('authorized_at')
            ->whereHas('catalogProductCompanySales', function ($cpcs) {
                $cpcs->whereHas('CatalogProductCompany', function ($cpc) {
                    $cpc->whereHas('CatalogProduct', function ($cp) {
                        $cp->whereHas('rawMaterials', function ($rm) {
                            $rm->where('raw_material_id', $this->id);
                        });
                    });
                });
            })
            ->get();

        $sales = $sales->where(function ($sale) {
            return $sale->getStatus()['label'] != 'Producción terminada';
        })->values();

        return $sales;
    }

    public function getCommitedUnits()
    {
        // buscar las OV los cuales algun producto ordenado contiene esta materia prima
        $sales = $this->getSalesCommited();

        // Filtrar los resultados utilizando el método filter
        $salesFiltered = $sales->sum(function ($sale) {
            // Obtener el valor de getStatus()
            $statusLabel = $sale->getStatus()['label'];
            if ($statusLabel != 'Producción terminada') {
                $product = $sale->catalogProductCompanySales->first(function ($cpcs) {
                    return $cpcs->catalogProductCompany?->catalogProduct->rawMaterials->contains('id', $this->id);
                });

                // Obtener la relación rawMaterials del elemento encontrado
                $rawMaterialsRelation = optional($product)->catalogProductCompany->catalogProduct->rawMaterials;

                // Filtrar los elementos en la relación rawMaterials por el ID
                $matchingRawMaterial = $rawMaterialsRelation->firstWhere('id', $this->id);

                return $matchingRawMaterial->pivot->quantity * ($product->quantity - $product->finished_product_used);
            }
        });

        return $salesFiltered;
    }
}
