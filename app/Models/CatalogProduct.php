<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CatalogProduct extends Model
{
    use HasFactory;

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

    //relationships

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class)->using(CatalogProductRawMaterial::class)
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

}
