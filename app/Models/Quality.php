<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quality extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'status',
        'product_inspection',
        'supervisor_id',
        'production_id'
    ];

    protected $casts = [
        'product_inspection' => 'array'
    ];

    //relationships
    public function supervisor () :BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function production () :BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
