<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OportunityTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'limit_date',
        'time',
        'finished_at',
        'description',
        'priority',
        'reminder',
        'user_id',
        'oportunity_id',
        'asigned_id',
    ];

    protected $casts = [
        'limit_date' => 'date',
        'finished_at' => 'datetime',
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

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
