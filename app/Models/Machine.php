<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Machine extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    // Método para verificar si necesita mantenimiento
    public function needsMaintenance()
    {
        if (!$this->maintenances->count()) {
            // La máquina no tiene mantenimientos registrados, por lo que necesita mantenimiento.
            return true;
        }

        $lastMaintenanceDate = $this->maintenances->max('created_at');
        $daysUntilMaintenance = $lastMaintenanceDate->addDays($this->days_next_maintenance)->diffInDays(now());

        return $daysUntilMaintenance <= 0;
    }

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
