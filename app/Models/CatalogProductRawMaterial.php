<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductRawMaterial extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'quantity',
        'raw_material_id',
        'catalog_product_id',
        'production_costs'
    ];

    protected $casts = [
        'production_costs' => 'array'
    ];

    

    public function productionCost() :BelongsTo
    {
        return $this->belongsTo(ProductionCost::class);
    }
}
