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
        'progress',
        'pause_justification',
    ];

    // relationships
    public function production() :BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
}
