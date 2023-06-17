<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RawMaterial extends Model
{
    use HasFactory;
  
  protected $fillable = [
        'name',
        'part_number',
        'description',
        'measure_unit',
        'min_quantity',
        'max_quantity',
        'cost',
        'features',
    ];

    protected $casts = [
        'features' => 'array'
    ];

    // relationships

    /**
     * Get the RawMaterial's sorage in warehose.
     */
    public function storage(): MorphOne
    {
        return $this->morphOne(Storage::class, 'storageable');
    }

    public function catalogProducts(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class)->using(CatalogProductRawMaterial::class)
                ->withPivot([
                    'quantity',
                ])->withTimestamps();
    }
}
