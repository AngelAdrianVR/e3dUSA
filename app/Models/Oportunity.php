<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Oportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact',
        'amount',
        'status',
        'priority',
        'finished_at',
        'estimated_finish_date',
        'company_id',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
        'estimated_finish_date' => 'date',
    ];

    //relationships
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
