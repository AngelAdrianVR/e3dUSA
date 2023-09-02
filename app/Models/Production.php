<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
        'is_paused',
        'additionals',
        'scrap',
    ];

    protected $casts = [
        'additionals' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    //relationships
    public function catalogProductCompanySale(): BelongsTo
    {
        return $this->belongsTo(CatalogProductCompanySale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale() :HasOneThrough
    {
        return $this->hasOneThrough(Sale::class, CatalogProductCompanySale::class, 'id', 'catalog_product_company_sale_id', 'catalog_product_company_sale_id', 'sale_id');
    }

    public function Progress() :HasMany
    {
        return $this->hasMany(ProductionProgress::class, 'production_id', 'id');
    }
}
