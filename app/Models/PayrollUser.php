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
        'start_break',
        'end_break',
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
        'start_break' => 'datetime:h:m',
        'end_break' => 'datetime:h:m',
        'check_out' => 'datetime:h:m',
        'additionals' => 'array',
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
        if ($this->start_break && $this->end_break) {
            $time = $this->start_break->diff($this->end_break);
            return "{$time->h}h {$time->i}m";
        }

        return null;
    }

    public function totalWorkedTime()
    {
        $user = $this->user;
        if (isset($user?->employee_properties['work_days'][$this->date->dayOfWeek]['check_out'])) {
            // El índice 'check_out' existe en el array
            $user_leave_time = Carbon::parse($user?->employee_properties['work_days'][$this->date->dayOfWeek]['check_out']);
        } else {
            // El índice 'check_out' no existe en el array
            $user_leave_time = Carbon::parse('15:00:00');
        }

        if ($this->check_in) {
            // date has passed (isPast also return true if date is today)
            if ($this->date->addDays(1)->isPast()) {
                $last_check = $this->check_in;
                if ($this->check_out) {
                    $last_check = $this->check_out;
                } elseif ($this->end_break) {
                    $last_check = $this->end_break;
                } elseif ($this->start_break) {
                    $last_check = $this->start_break;
                }

                if ($last_check->greaterThan($user_leave_time)) {
                    $last_check = $user_leave_time->isoFormat('HH:mm');
                }

                $time = $this->check_in->diffInMinutes($last_check);
            } else {
                // compare current time vs check out user time & restrict time worked
                if (now()->greaterThan($user_leave_time)) {
                    $time = $this->check_in->diffInMinutes($this->check_out ?? $user_leave_time);
                } else {
                    $time = $this->check_in->diffInMinutes($this->check_out ?? now());
                }
            }
            if ($this->start_break) {
                $break = $this->start_break->diffInMinutes($this->end_break ?? $this->start_break);
            } else {
                $break = 0;
            }

            $time -= $break;

            $hours = intval($time / 60);
            $minutes = $time % 60;

            return [
                'formatted' => "{$hours}h {$minutes}m",
                'hours' => round($time / 60, 2),
            ];
        } else if ($this->justification_event_id === 2) {
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
            $break = $this->start_break->diffInMinutes($this->end_break ?? $this->start_break);

            // sub break and 8 hrs
            $time -= ($break + 60 * 8);
            if ($time < 0) $time = 0;

            $hours = intval($time / 60);
            $minutes = $time % 60;

            $amount = $this->additionals['salary']['hour'] * ($time / 60);

            return ['formatted' => "{$hours}h {$minutes}m", 'minutes' => $time, 'amount' => ['number_format' => number_format($amount, 2), 'raw' => $amount]];
        }

        return ['formatted' => "0h 0m", 'minutes' => 0, 'amount' => ['number_format' => 0.00, 'raw' => 0]];
    }
}
