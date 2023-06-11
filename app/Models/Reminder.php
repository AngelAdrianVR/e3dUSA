<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'remind_at',
        'user_id',
    ];

    // relationships
    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
