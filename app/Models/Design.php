<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Design extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'plans_image',
        'logo_image',
        'company_branch_name',
        'contact_name',
        'dimensions',
        'status',
        'design_data',
        'specifications',
        'pantones',
        'has_priority',
        'needs_authorization',
        'designer_id',
        'design_type_id',
        'user_id',
        'measure_unit',
        'authorized_user_name',
        'authorized_at',
        'expected_end_at',
        'original_design_id',
        'is_complex',
        'reuse_percentage',
        'started_at',
        'finished_at',
        'design_modifications',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'expected_end_at' => 'datetime',
        'design_modifications' => 'array'
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designer()
    {
        return $this->belongsTo(User::class);
    }

    public function designType()
    {
        return $this->belongsTo(DesignType::class);
    }

    public function originalDesign()
    {
        return $this->belongsTo(self::class);
    }

    public function modifications()
    {
        return $this->hasMany(DesignModification::class);
    }

    // methods
    public function getTotalTimeInMinutes()
    {
        if ($this->started_at && $this->finished_at) {
            $totalTime = $this->started_at->diffInMinutes($this->finished_at);

            // Obtén al usuario asociado a la orden
            $user = $this->designer;

            if ($user) {
                // Obtén los días laborables del usuario
                $workDays = $user->employee_properties['work_days'];

                // Filtra los días no laborables (null)
                $workDays = array_filter($workDays, function ($day) {
                    return $day['check_in'] !== null;
                });

                // Obtén los días en los que se trabajó en la orden
                $workedDays = collect($workDays)->map(function ($day) {
                    return $day['day'];
                });

                // Filtra los días en los que la orden continuó después de la hora de salida
                $workedDays = $workedDays->filter(function ($day) use ($user) {
                    $checkOutTime = isset($user->employee_properties['work_days'][$day]['check_out']) ? Carbon::parse($user->employee_properties['work_days'][$day]['check_out']) : null;
                    $finishedTime = $this->finished_at->copy()->startOfDay();

                    return $finishedTime->gt($checkOutTime);
                });

                // Resta los minutos de los días no laborables y los minutos excedidos
                $totalTime -= count($workedDays) * 24 * 60;
            }

            return $totalTime;
        }

        return 0;
    }
}
