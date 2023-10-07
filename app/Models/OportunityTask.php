<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OportunityTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'limite_date',
        'time',
        'description',
        'priority',
        'reminder',
        'user_id',
        'oportunity_id',
        'asigned_id',
    ];

    protected $casts = [
        'limite_date' => 'datetime',
    ];

    //relationships
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asigned():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function oportunity():BelongsTo
    {
        return $this->belongsTo(Oportunity::class);
    }
}
