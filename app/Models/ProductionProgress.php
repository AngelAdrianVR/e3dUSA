<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'task',
        'progress',
        'notes',
        'finished_at',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    // relationships
    public function production() :BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
}
