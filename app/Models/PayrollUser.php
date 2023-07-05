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
     public function user() :BelongsTo
     {
         return $this->belongsTo(User::class);
     }
 
     public function payroll() :BelongsTo
     {
         return $this->belongsTo(Payroll::class);
     }

     public function justificationEvent() :BelongsTo
     {
         return $this->belongsTo(JustificationEvent::class);
     }

    //  methods
     public function totalBreakTime()
     {
        if($this->start_break && $this->end_break) {
            $time = $this->start_break->diff($this->end_break);
            return "{$time->h}h {$time->i}m";
        }

        return null;
     }

     public function totalWorkedTime()
     {
        if($this->check_in) {
            $time = $this->check_in->diffInMinutes($this->check_out ?? now());
            $break = $this->start_break->diffInMinutes($this->end_break ?? $this->start_break);
            
            $time -= $break;
    
            $hours = intval($time / 60);
            $minutes = $time % 60;
    
            return "{$hours}h {$minutes}m";
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
     
}
