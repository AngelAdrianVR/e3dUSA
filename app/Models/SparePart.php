<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SparePart extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'quantity',
        'supplier',
        'cost',
        'description',
        'location',
        'machine_id',
    ];

    // relationships
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
