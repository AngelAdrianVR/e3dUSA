<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'storage_id',
        'type',
        'quantity',
        'notes',
    ];

    // relashionships
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storage() : BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
}
