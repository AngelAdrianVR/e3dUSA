<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PayrollUser extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'date',
        'check_in',
        'check_out',
        'pausas',
        'late',
        'extras_enabled',
        'extra_hours',
        'extra_minutes',
        'user_id',
        'payroll_id',
        'justification_event_id',
        'additionals',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:h:m',
        'check_out' => 'datetime:h:m',
        'additionals' => 'array',
        'pausas' => 'array',
    ];

    // relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function justificationEvent(): BelongsTo
    {
        return $this->belongsTo(JustificationEvent::class);
    }

    public function additionalTimeRequest(): HasOne
    {
        return $this->hasOne(AdditionalTimeRequest::class, 'payroll_user_id', 'id');
    }

    //  methods
    public function totalBreakTime()
    {
        if (!empty($this->pausas)) {
            $totalBreakTime = $this->calculateTotalBreakTime();
            $totalBreakTime = $this->getTotalBreakWithoutFixedBreakMinutes($totalBreakTime);;

            $hours = intval($totalBreakTime / 60);
            $minutes = $totalBreakTime % 60;

            return "{$hours}h {$minutes}m";
        }

        return null;
    }

    public function totalWorkedTime()
    {
        if ($this->check_in) {
            if ($this->date->addDays(1)->isPast() && !$this->check_out) {
                return [
                    'formatted' => "0h 0m",
                    'hours' => 0,
                ];
            } else {
                $workedTime = $this->calculateWorkedTime();

                $breakTime = $this->calculateTotalBreakTime();
                $workedTime -= $breakTime;
                // $workedTime -= $this->getTotalBreakWithoutFixedBreakMinutes($breakTime);

                // Dia marcado como descanso lo esta trabajando
                if ($this->user->employee_properties['work_days'][$this->date->dayOfWeek]['hours'] == 0) {
                    $maxWorkedTime = 480; //8 horas limite
                } else {
                    $maxWorkedTime = $this->user->employee_properties['work_days'][$this->date->dayOfWeek]['hours'] * 60;
                }

                // aumentar el maximo permitido por dia si existe una solicitud de tiempo adicional autorizada
                $additional_time = $this->additionalTimeRequest;
                $requested_in_minutes = 0;
                if ($additional_time && $additional_time?->authorized_at) {
                    $requested = explode(':', $additional_time->time_requested);
                    $requested_in_minutes = intval($requested[0]) * 60 + intval($requested[1]);
                }

                $maxWorkedTime += $requested_in_minutes;
                $workedTime = min($workedTime, $maxWorkedTime);

                $hours = intval($workedTime / 60);
                $minutes = $workedTime % 60;

                return [
                    'formatted' => "{$hours}h {$minutes}m",
                    'hours' => round($workedTime / 60, 2),
                ];
            }
        } elseif ($this->justification_event_id === 2) {
            $time = $this->user->employee_properties['work_days'][$this->date->dayOfWeek]['hours'] * 60;

            $hours = intval($time / 60);
            $minutes = $time % 60;

            return [
                'formatted' => "{$hours}h {$minutes}m",
                'hours' => round($time / 60, 2),
            ];
        }

        return null;
    }

    public function getTotalBreakWithoutFixedBreakMinutes($total_break) 
    {
        $fixed_break_minutes = 0;

        // minutos de comida fijos que se toman en cuenta siempre para el calculo del tiempo trabajado
        if (array_key_exists('break', $this->user->employee_properties['work_days'][$this->date->dayOfWeek])) {
            $fixed_break_minutes = $this->user->employee_properties['work_days'][$this->date->dayOfWeek]['break'];
        }

        // restar minutos de comida a pausas totales
        $total = $total_break - $fixed_break_minutes;

        // si pausó menos minutos que el total de su comida, simplemente queda en 0 las pausas totales a cuenta de comida
        if ($total < 0) $total = 0;

        return $total;
    }

    public function getLateTime()
    {
        if ($this->check_in && $this->user->employee_properties['work_days'][$this->date->dayOfWeek]['check_in'] != 0) {
            $original_check_in = Carbon::parse($this->user->employee_properties['work_days'][$this->date->dayOfWeek]['check_in']);
            $minutes_late = $original_check_in->diffInMinutes($this->check_in, false);
            return $minutes_late < 0 ? 0 : $minutes_late;
        }

        return 0;
    }

    public function getExtras()
    {
        if ($this->check_in && !$this->justification_event_id && $this->extras_enabled) {
            $time = $this->check_in->diffInMinutes($this->check_out ?? now());
            $breakTime = $this->calculateTotalBreakTime();

            // subtract break and 8 hrs
            $time -= ($this->$breakTime + 60 * 8);
            if ($time < 0) {
                $time = 0;
            }

            $hours = intval($time / 60);
            $minutes = $time % 60;

            $amount = $this->calculateExtrasAmount($time);

            return [
                'formatted' => "{$hours}h {$minutes}m",
                'minutes' => $time,
                'amount' => [
                    'number_format' => number_format($amount, 2),
                    'raw' => $amount,
                ],
            ];
        }

        return [
            'formatted' => "0h 0m",
            'minutes' => 0,
            'amount' => [
                'number_format' => number_format(0.00, 2),
                'raw' => 0,
            ],
        ];
    }

    // private methods
    private function calculateExtrasAmount($time)
    {
        $hourlySalary = $this->additionals['salary']['hour'];
        return $hourlySalary * ($time / 60);
    }

    private function getLastCheckTime()
    {
        $last_check = now();

        if ($this->check_out) {
            $last_check = $this->check_out;
        }

        return $last_check;
    }

    private function calculateWorkedTime()
    {
        $lastCheck = $this->getLastCheckTime();

        return $this->check_in->diffInMinutes($lastCheck);
    }

    private function calculateTotalBreakTime()
    {
        $totalBreakTime = 0;

        if ($this->pausas) {
            foreach ($this->pausas as $pausa) {
                $start = Carbon::parse($pausa['start']);
                $finish = $pausa['finish'] ? Carbon::parse($pausa['finish']) : now();

                $breakTime = $start->diffInMinutes($finish);
                $totalBreakTime += $breakTime;
            }
        }

        if ($totalBreakTime)


            return $totalBreakTime;
    }
}
