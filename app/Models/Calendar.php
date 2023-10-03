<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'participants',
        'description',
        'status',
        'start_date',
        'finish_date',
        'start_at',
        'finish_at',
        'user_id',  
    ];

    protected $casts = [
        'start_date' => 'date',
        'finish_date' => 'date',
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
        'participants' => 'array',
    ];

    //relaionships
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
