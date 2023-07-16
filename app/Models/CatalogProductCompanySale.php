<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductCompanySale extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'notes',
        'status',
        'sale_id',
        'catalog_product_company_id',
    ];

    protected $casts = [
    ];

    //relationships
    public function production(): HasOne
    {
        return $this->hasOne(Production::class);
    }

    public function catalogProductCompany(): BelongsTo
    {
        return $this->belongsTo(CatalogProductCompany::class);
    }

}
