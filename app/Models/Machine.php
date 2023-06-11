<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'weight',
        'width',
        'large',
        'height',
        'cost',
        'supplier',
        'aquisition_date',
        'days_next_maintenance',
    ];

    protected $casts = [
        'aquisition_date' => 'date',
    ];

    // relationships
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function spareParts()
    {
        return $this->hasMany(SparePart::class);
    }
}
