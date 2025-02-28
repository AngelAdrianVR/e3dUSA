<?php

namespace App\Models;

use Carbon\Carbon;
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
        'scrap_reason',
        'supervision',
        'good_units',
        'packages',
        'has_low_stock',
    ];

    protected $casts = [
        'additionals' => 'array',
        'packages' => 'array',
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

    public function sale(): HasOneThrough
    {
        return $this->hasOneThrough(Sale::class, CatalogProductCompanySale::class, 'id', 'catalog_product_company_sale_id', 'catalog_product_company_sale_id', 'sale_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ProductionProgress::class, 'production_id', 'id');
    }

    // methods
    public function getTotalTimeInMinutes()
    {
        // Verifica si started_at y finished_at son diferentes de null
        if ($this->started_at !== null && $this->finished_at !== null) {
            // Calcula la diferencia en minutos
            $totalTime = $this->started_at->diffInMinutes($this->finished_at);
        } else {
            $totalTime = 0;
        }

        return $totalTime;
    }

    public function getPausaTimeInMinutes()
    {
        $pausaTime = 0;

        // Recorre el progreso para calcular el tiempo pausado
        foreach ($this->progress as $progress) {
            if ($progress->finished_at !== null) {
                $pausaTime += $progress->created_at->diffInMinutes($progress->finished_at);
            }
        }

        return $pausaTime;
    }
}
