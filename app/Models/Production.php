<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'user_id',
        'catalog_product_company_sale_id',
        'tasks',
        'estimated_time_hours',
        'estimated_time_minutes',
        'started_at',
        'finished_at',
        'additionals',
    ];

    protected $casts = [
        'additionals' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    //relationships
    public function catalogProductCompanySale(): BelongsTo
    {
        return $this-> belongsTo(CatalogProductCompanySale::class);
    }

    public function user(): BelongsTo
    {
        return $this-> belongsTo(User::class);
    }
}
