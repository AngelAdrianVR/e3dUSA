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

        if (!$firstProduction || $firstProduction->started_at === null) {
            return "Aún no se inicia la primera tarea";
        }

        $estimatedStart = $firstProduction->started_at->subHours(6);
        $totalMinutes = 0;

        foreach ($this->productions as $production) {
            $totalMinutes += ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
        }

        $currentDateTime = $estimatedStart;
        $remainingMinutes = $totalMinutes;
        $today = now();

        // Obtener días laborales y asistencias
        $workDays = collect($firstProduction->operator->employee_properties["work_days"]);
        $activePayroll = Payroll::firstWhere('is_active', true);
        $attendances = PayrollUser::where([
            'user_id' => $firstProduction->operator->id,
            'payroll_id' => $activePayroll->id,
        ])->get();

        while ($remainingMinutes > 0) {
            $dayOfWeek = $currentDateTime->dayOfWeek;
            $workDay = $workDays->firstWhere('day', $dayOfWeek);

            if ($workDay && $workDay['check_in'] && $workDay['check_out']) {
                $checkInTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_in'])->subHours(6);
                $checkOutTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_out'])->subHours(6);
                $breakMinutes = $workDay['break'] ?? 0;

                // Solo verificar asistencias si el día ya ha pasado o es hoy
                if ($currentDateTime->lessThanOrEqualTo($today)) {
                    $attendanceToday = $attendances->first(function ($attendance) use ($currentDateTime) {
                        return trim((string)$attendance->date->toDateString()) == $currentDateTime->toDateString();
                    });

                    if ($currentDateTime->isToday()) {
                        // Escenario para hoy
                        if (!$attendanceToday || !$attendanceToday->check_in) {
                            // Si no tiene entrada, sumar el tiempo desde la hora de entrada hasta ahora
                            $missedMinutesToday = $checkInTime->diffInMinutes(now());
                            $remainingMinutes += $missedMinutesToday;
                        }
                        // Si tiene entrada pero no salida, no se toma como falta y no se suma tiempo.
                    } else {
                        // Escenario para días pasados
                        if (!$attendanceToday || !$attendanceToday->check_in || !$attendanceToday->check_out) {
                            // Sumar el tiempo laboral completo del día al tiempo restante
                            $missedDayMinutes = $checkInTime->diffInMinutes($checkOutTime) - $breakMinutes;
                            $remainingMinutes += $missedDayMinutes;
                        } else {
                            // Verificar si el operador llegó tarde ****** VERIFICAR ******
                            $checkInActual = $attendanceToday->check_in;
                            if ($checkInActual->greaterThan($checkInTime)) {
                                // Calcular los minutos de tardanza
                                $lateMinutes = $checkInActual->diffInMinutes($checkInTime);
                                $remainingMinutes += $lateMinutes;
                            }
                        }
                    }
                }

                $startTime = max($currentDateTime, $checkInTime);
                $remainingDayMinutes = $startTime->diffInMinutes($checkOutTime) - $breakMinutes;

                $workMinutesToday = min($remainingMinutes, $remainingDayMinutes);
                $remainingMinutes -= $workMinutesToday;

                $currentDateTime = $startTime->copy()->addMinutes($workMinutesToday + $breakMinutes);
            }

            if ($remainingMinutes > 0) {
                do {
                    $currentDateTime->addDay();
                    $nextWorkDay = $workDays->firstWhere('day', $currentDateTime->dayOfWeek);
                } while (!$nextWorkDay || !$nextWorkDay['check_in']);

                if ($nextWorkDay && $nextWorkDay['check_in']) {
                    $currentDateTime->setTimeFromTimeString($nextWorkDay['check_in'])->subHours(6);
                }
            }
        }

        return $currentDateTime->addHours(6);
    }

    // public function getEstimatedCompletionDate()
    // {
    //     $firstProduction = $this->productions()->orderBy('started_at')->first();
    //     // Verificar si hay una tarea iniciada
    //     if (!$firstProduction || $firstProduction->started_at === null) {
    //         return "Aún no se inicia la primera tarea";
    //     }

    //     $estimatedStart = $firstProduction->started_at->subHours(6);

    //     $totalMinutes = 0;

    //     // Sumar el tiempo estimado de todas las tareas
    //     foreach ($this->productions as $production) {
    //         $totalMinutes += ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
    //     }

    //     $currentDateTime = $estimatedStart;
    //     $remainingMinutes = $totalMinutes;

    //     // Obtener el horario de trabajo del operador
    //     $workDays = collect($firstProduction->operator->employee_properties["work_days"]);

    //     // Sumar el tiempo respetando los horarios laborales
    //     while ($remainingMinutes > 0) {
    //         $dayOfWeek = $currentDateTime->dayOfWeek;
    //         $workDay = $workDays->firstWhere('day', $dayOfWeek);

    //         // Verificar si el operador trabaja en este día
    //         if ($workDay && $workDay['check_in'] && $workDay['check_out']) {
    //             $checkInTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_in'])->subHours(6);
    //             $checkOutTime = $currentDateTime->copy()->setTimeFromTimeString($workDay['check_out'])->subHours(6);
    //             $breakMinutes = $workDay['break'] ?? 0;

    //             // Calcular el inicio del trabajo para el día, en función de la hora actual o la hora de entrada
    //             $startTime = max($currentDateTime, $checkInTime);
    //             $remainingDayMinutes = $startTime->diffInMinutes($checkOutTime) - $breakMinutes;

    //             // Calcular el tiempo que puede trabajar este día
    //             $workMinutesToday = min($remainingMinutes, $remainingDayMinutes);

    //             // Restar el tiempo trabajado del total restante
    //             $remainingMinutes -= $workMinutesToday;

    //             // Avanzar el tiempo actual
    //             $currentDateTime = $startTime->copy()->addMinutes($workMinutesToday + $breakMinutes);
    //         }

    //         // Avanzar al siguiente día laboral iniciando a la hora de entrada del operador
    //         if ($remainingMinutes > 0) {
    //             do {
    //                 // Avanza un día
    //                 $currentDateTime->addDay();

    //                 // Encuentra el próximo día laboral del operador
    //                 $nextWorkDay = $workDays->firstWhere('day', $currentDateTime->dayOfWeek);
    //             } while (!$nextWorkDay || !$nextWorkDay['check_in']); // Repite hasta encontrar un día laboral con hora de entrada

    //             // Configura la hora de entrada del próximo día laboral
    //             if ($nextWorkDay && $nextWorkDay['check_in']) {
    //                 $currentDateTime->setTimeFromTimeString($nextWorkDay['check_in'])->subHours(6);
    //             }
    //         }
    //     }
    //     return $currentDateTime->addHours(6);
    //     // return $currentDateTime->addHours(6)->isoFormat('DD MMM, YYYY h:mm A');
    // }
}
