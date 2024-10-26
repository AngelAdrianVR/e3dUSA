<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

// use Illuminate\Database\Eloquent\Relations\Pivot;

// class CatalogProductCompanySale extends Pivot
class CatalogProductCompanySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'finished_product_used',
        'notes',
        'status',
        'sale_id',
        'is_new_design',
        'confusion_alert',
        'requires_medallion',
        'catalog_product_company_id',
        'traveler_data',
    ];

    protected $table = 'catalog_product_company_sale';

    protected $casts = [
        'traveler_data' => 'array',
    ];

    //relationships
    public function productions(): HasMany
    {
        return $this->HasMany(Production::class);
    }

    public function catalogProductCompany(): BelongsTo
    {
        return $this->belongsTo(CatalogProductCompany::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getEstimatedCompletionDate()
    {
        $firstProduction = $this->productions()->orderBy('started_at')->first();
        // Verificar si hay una tarea iniciada
        if (!$firstProduction || $firstProduction->started_at === null) {
            return "Aún no se inicia la primera tarea";
        }

        $estimatedStart = $firstProduction->started_at->subHours(6);

        $totalMinutes = 0;

        // Sumar el tiempo estimado de todas las tareas
        foreach ($this->productions as $production) {
            $totalMinutes += ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
        }

        $currentDateTime = $estimatedStart;
        $remainingMinutes = $totalMinutes;

        // Obtener el horario de trabajo del operador
        $workDays = collect($firstProduction->operator->employee_properties["work_days"]);

        // Sumar el tiempo respetando los horarios laborales
        while ($remainingMinutes > 0) {
            $dayOfWeek = $currentDateTime->dayOfWeek;
            $workDay = $workDays->firstWhere('day', $dayOfWeek);

            // Verificar si el operador trabaja en este día
            if ($workDay && $workDay['check_in'] && $workDay['check_out']) {
                $checkInTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_in'])->subHours(6);
                $checkOutTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_out'])->subHours(6);
                $breakMinutes = $workDay['break'];

                // Calcular el inicio del trabajo para el día, en función de la hora actual o la hora de entrada
                $startTime = max($currentDateTime, $checkInTime);
                $remainingDayMinutes = $startTime->diffInMinutes($checkOutTime) - $breakMinutes;

                // Calcular el tiempo que puede trabajar este día
                $workMinutesToday = min($remainingMinutes, $remainingDayMinutes);

                // Restar el tiempo trabajado del total restante
                $remainingMinutes -= $workMinutesToday;

                // Avanzar el tiempo actual
                $currentDateTime = $startTime->copy()->addMinutes($workMinutesToday + $breakMinutes);
            }

            // Avanzar al siguiente día laboral iniciando a la hora de entrada del operador
            if ($remainingMinutes > 0) {
                $currentDateTime->addDay();
                $nextWorkDay = $workDays->firstWhere('day', $currentDateTime->dayOfWeek);
                // Configura la hora de entrada del próximo día laboral
                $currentDateTime->setTimeFromTimeString($nextWorkDay['check_in'] ?? '00:00:00');
            }
        }
        return $currentDateTime->addHours(6)->isoFormat('DD MMM, YYYY h:mm A');
    }
}
