<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'week',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date'
    ];

    //Rellationships
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(PayrollUser::class)
            ->withPivot([
                'id',
                'date',
                'check_in',
                'start_break',
                'end_break',
                'check_out',
                'late',
                'extras_enabled',
                'extra_hours',
                'extra_minutes',
                'additionals',
            ])
            ->withTimestamps();
    }
}
