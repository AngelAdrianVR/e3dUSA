<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Maintenance extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const PREVENTIVE = 0, CORRECTIVE = 1;

    protected $fillable = [
        'problems',
        'actions',
        'cost',
        'maintenance_type_id',
        'responsible',
        'machine_id',
    ];

    // relationships
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
