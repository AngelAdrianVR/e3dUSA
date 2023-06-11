<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_number',
        'description',
        'meaure_unit',
        'min_quantity',
        'max_quantity',
        'cost',
        'features',
    ];

    protected $casts = [
        'features' => 'array'
    ];

    //relationships

    public function catalogProducts(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class)->using(CatalogProductRawMaterial::class)
                ->withPivot([
                    'quantity',
                ])->withTimestamps();
    }
}
