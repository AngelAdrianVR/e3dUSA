<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CatalogProductCompanySale extends Pivot
{
    use HasFactory;

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

    

}
