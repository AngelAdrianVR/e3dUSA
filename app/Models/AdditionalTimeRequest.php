<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalTimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'justification',
        'time_requested',
        'comments',
        'authorized_user_name',
        'authorized_at',
        'payroll_id', // quitar*
        'user_id', //quitar*
        'payroll_user_id',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
    ];

    // relationships
    public function payrollUser() :BelongsTo
    {
        return $this->belongsTo(PayrollUser::class);
    }

    public function user() :BelongsTo //quitar*
    {
        return $this->belongsTo(User::class);
    }

}
