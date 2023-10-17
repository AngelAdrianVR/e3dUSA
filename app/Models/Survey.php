<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'oportunity_id',
    ];

    //relationships
    public function oportunity() :BelongsTo
    {
       return $this->belongsTo(Oportunity::class);
    }
}
