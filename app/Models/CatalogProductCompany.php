<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductCompany extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'oldest_price',
        'oldest_date',
        'oldest_currency',
        'old_date',
        'new_date',
        'old_price',
        'new_price',
        'old_currency',
        'new_currency',
        'catalog_product_id',
        'company_id',
        'user_id',
    ];

    protected $casts = [
        'oldest_date' => 'datetime',
        'old_date' => 'datetime',
        'new_date' => 'datetime',
    ];

    //relationships

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function catalogProduct():BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingRates():HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

}
