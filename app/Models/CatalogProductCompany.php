<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductCompany extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'quantity',
        'notes',
        'status',
        'assigned_jobs',
        'sale_id',
        'catalog_product_company_id',
    ];

    protected $casts = [
        'assigned_jobs' => 'array'
    ];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class)
            ->withPivot('quantity', 'notes', 'status', 'assigned_jobs')
            ->withTimestamps();
    }

}
