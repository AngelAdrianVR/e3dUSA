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

    // public function getEstimatedCompletionDate()
    // {
    //     if (!$this->started_at) return 'Orden no iniciada aún';

    //     $workDays = collect($this->operator->employee_properties["work_days"]);
    //     $totalEstimatedMinutes = ($this->estimated_time_hours * 60) + $this->estimated_time_minutes;
    //     $currentDateTime = $this->started_at; // Iniciar desde el momento en que se inicia la orden
    //     $remainingMinutes = $totalEstimatedMinutes;

    //     // Iterar día por día
    //     while ($remainingMinutes > 0) {
    //         $dayOfWeek = $currentDateTime->dayOfWeek;
    //         $workDay = $workDays->firstWhere('day', $dayOfWeek);

    //         if ($workDay && $workDay['check_in'] && $workDay['check_out']) {
    //             $checkInTime = Carbon::parse($workDay['check_in']);
    //             $checkOutTime = Carbon::parse($workDay['check_out']);
    //             $breakMinutes = $workDay['break'];

    //             // Calcular el inicio de la jornada según la hora actual o el horario de entrada
    //             $startTime = $currentDateTime->greaterThan($checkInTime) ? $currentDateTime : $checkInTime;

    //             // Calcular los minutos restantes de la jornada actual
    //             $remainingDayMinutes = $startTime->diffInMinutes($checkOutTime) - $breakMinutes;

    //             // Calcular los minutos trabajables en el día actual
    //             $dailyWorkMinutes = min($remainingMinutes, $remainingDayMinutes);

    //             // Restar el tiempo trabajado al tiempo restante
    //             $remainingMinutes -= $dailyWorkMinutes;

    //             // Avanzar el tiempo de finalización estimado desde la hora actual
    //             $currentDateTime = $startTime->copy()->addMinutes($dailyWorkMinutes + $breakMinutes);
    //         }

    //         // Moverse al siguiente día si aún queda tiempo por trabajar
    //         $currentDateTime->addDay()->startOfDay();
    //     }

    //     return $currentDateTime;
    // }
}
