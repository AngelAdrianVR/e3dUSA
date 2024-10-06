<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'sent_at',
        'sale_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    //relationships
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
