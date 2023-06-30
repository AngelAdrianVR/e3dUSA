<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollUser extends Model
{
    use HasFactory;

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
        'check_in' => 'datetime: h:mm',
        'start_break' => 'datetime: h:mm',
        'end_break' => 'datetime: h:mm',
        'check_out' => 'datetime: h:mm',
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
}
