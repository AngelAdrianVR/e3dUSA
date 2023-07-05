<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

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
