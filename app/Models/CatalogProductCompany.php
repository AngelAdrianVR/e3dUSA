<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductCompany extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'old_date',
        'new_date',
        'old_price',
        'new_price',
        'old_currency',
        'new_currency',
        'catalog_product_id',
        'company_id',
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

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class)
            ->withPivot('quantity', 'notes', 'status', 'assigned_jobs')
            ->withTimestamps()
            ->using(CatalogProductCompanySale::class);
    }
}
