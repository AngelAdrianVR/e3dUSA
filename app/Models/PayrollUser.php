<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PayrollUser extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'date',
        'check_in',
        'pausas',
        'check_out',
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

    //  methods
    public function totalBreakTime()
    {
        if (!empty($this->pausas)) {
            $totalBreakTime = $this->calculateTotalBreakTime();

            $hours = intval($totalBreakTime / 60);
            $minutes = $totalBreakTime % 60;

            return "{$hours}h {$minutes}m";
        }

        return null;
    }

    public function totalWorkedTime()
    {
        if ($this->check_in) {
            $workedTime = $this->calculateWorkedTime();

            $breakTime = $this->calculateTotalBreakTime();
            $workedTime -= $breakTime;

            $maxWorkedTime = $this->user->employee_properties['hours_per_day'] * 60;
            $workedTime = min($workedTime, $maxWorkedTime);

            $hours = intval($workedTime / 60);
            $minutes = $workedTime % 60;

            return [
                'formatted' => "{$hours}h {$minutes}m",
                'hours' => round($workedTime / 60, 2),
            ];
        } elseif ($this->justification_event_id === 2) {
            $time = $this->user->employee_properties['hours_per_day'] * 60;

            $hours = intval($time / 60);
            $minutes = $time % 60;

            return [
                'formatted' => "{$hours}h {$minutes}m",
                'hours' => round($time / 60, 2),
            ];
        }

        return null;
    }
    
    public function getLateTime()
    {
        if ($this->check_in) {
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
            $time -= ($breakTime + 60 * 8);
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

        return $totalBreakTime;
    }
}
