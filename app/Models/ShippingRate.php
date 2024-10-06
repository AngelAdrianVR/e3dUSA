<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'boxes',
        'catalog_product_id',
    ];

    protected $casts = [
        'boxes' => 'Array'
    ];

    //relationships
    public function catalogProduct() :BelongsTo
    {
       return $this->belongsTo(CatalogProduct::class);
    }
}
