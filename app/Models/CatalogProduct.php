<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CatalogProduct extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
  
  protected $fillable = [
        'name',
        'description',
        'part_number',
        'measure_unit',
        'cost',
        'min_quantity',
        'max_quantity',
        'features',
    ];

    protected $casts = [        
        'features' => 'array',
    ];

    // relationships

    /**
     * Get the CatalogProduct's storage in warehouse.
     */
    public function storages(): MorphMany
    {
        return $this->morphMany(Storage::class, 'storageable');
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class)->using(CatalogProductRawMaterial::class)
                ->withPivot([
                    'quantity',
                    'production_costs',
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

    public function companies(): BelongsToMany
    {
      return $this->belongsToMany(Quote::class)
            ->withPivot([
                'old_price',
                'old_date',
                'old_currency',
                'new_price',
                'new_date',
                'new_currency',
            ])->withTimestamps()
            ->using(CatalogProductCompany::class);
    }
}
