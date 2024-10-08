<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_product_id',
        'quantity',
        'boxes',
    ];

    protected $casts = [
        'boxes' => 'array'
    ];

    //relationships
    public function catalogProduct() :BelongsTo
    {
       return $this->belongsTo(CatalogProduct::class);
    }
}