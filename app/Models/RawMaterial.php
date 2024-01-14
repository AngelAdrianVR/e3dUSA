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

    // relationships

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

    // methods
    public function isInCatalogProduct()
    {
        return $this->catalogProducts()->exists();
    }
}
