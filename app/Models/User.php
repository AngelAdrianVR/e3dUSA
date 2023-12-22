<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'employee_properties',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'employee_properties' => 'array',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    //Relationships
    public function extraTimeRequests()
    {
        return $this->hasMany(ExtraTimeRequest::class, 'operator_id', 'id');
    }

    public function payrolls()
    {
        return $this->belongsToMany(Payroll::class)
            ->using(PayrollUser::class)
            ->withPivot([
                'id',
                'date',
                'check_in',
                'pausas',
                'check_out',
                'late',
                'extras_enabled',
                'extra_hours',
                'extra_minutes',
                'additionals',
            ])
            ->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany(Design::class, 'designer_id', 'id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'user_id', 'id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'user_id', 'id');
    }

    public function catalogProductsCompany()
    {
        return $this->hasMany(CatalogProductCompany::class, 'user_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id', 'id');
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'operator_id', 'id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class)
            ->withPivot([
                'id',
                'comments',
                'attendance_confirmation',
            ])
            ->withTimestamps();
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user', 'user_id', 'task_id');
    }

    // methods
    public function getNextAttendance()
    {
        $next = '';
        $today_attendance = PayrollUser::where('user_id', $this->id)->whereDate('date', today())->first();
        if (is_null($today_attendance)) {
            $next = 'Registrar entrada';
        } elseif (is_null($today_attendance->check_out)) {
            $next = 'Registrar salida';
        } else {
            $next = 'Dia terminado';
        }

        return $next;
    }

    public function getPauseStatus()
    {
        $today_attendance = PayrollUser::where('user_id', $this->id)->whereDate('date', today())->first();

        $pausas = $today_attendance?->pausas;

        if ($pausas && !isset(end($pausas)['finish'])) {
            return true;
        }

        return false;
    }
    
    public function getPendentProductions()
    {
        $pendent_productions = $this->productions()
        ->where('is_paused', true)
        ->whereNotNull('started_at')
        ->whereNull('finished_at')
        ->get()->count();

        return $pendent_productions;
    }

    public function setAttendance()
    {
        $next = '';
        $bonuses = [];
        $discounts = [];
        foreach ($this->employee_properties['bonuses'] as $bonus_id) {
            $bonus = Bonus::find($bonus_id);
            $bonuses[] = [
                'id' => $bonus_id,
                'amount' => $this->employee_properties['hours_per_week'] >= 48
                    ? $bonus->full_time
                    : $bonus->half_time
            ];
        }
        foreach ($this->employee_properties['discounts'] as $discount_id) {
            $discount = Discount::find($discount_id);
            $discounts[] = [
                'id' => $discount_id,
                'amount' => $discount->amount
            ];
        }

        $today_attendance = PayrollUser::firstOrCreate(['date' => today()->toDateString(), 'user_id' => $this->id], [
            'payroll_id' => Payroll::getCurrent()->id,
            'additionals' => [
                'salary' =>  [
                    "week" => $this->employee_properties['salary']['week'],
                    "day" => $this->employee_properties['work_days'][today()->dayOfWeek]["salary"],
                    "hour" => $this->employee_properties['salary']['hour'],
                ],
                'bonuses' => $bonuses,
                'discounts' => $discounts,
                'hours_per_week' => $this->employee_properties['hours_per_week'],
            ],
        ]);

        $today_attendance->update(['late' => $today_attendance->getLateTime()]);

        $now_time = now()->isoFormat('HH:mm');

        if (is_null($today_attendance->check_in)) {
            // revisar vacaciones y actualizar si ya es requerido
            $this->updateVacationProperties();

            $today_attendance->update([
                'check_in' => $now_time,
                'late' => $today_attendance->getLateTime(),
            ]);
            $next = 'Registrar salida';
        } elseif (is_null($today_attendance->check_out)) {
            $today_attendance->update([
                'check_out' => $now_time,
            ]);
            $next = 'Dia terminado';
        }

        return $next;
    }

    public function setPause()
    {
        $today_attendance = PayrollUser::where('date', today()->toDateString())->where('user_id', $this->id)->first();
        $new_record = ['start' => now()->toTimeString(), 'finish' => null];
        $is_paused = true;
        $pausas = $today_attendance->pausas;

        if ($pausas) {
            $last_record_key = array_key_last($pausas);
            $last_record = &$pausas[$last_record_key]; // Obtén una referencia al último registro

            if ($last_record['finish']) {
                // create new record
                $pausas[] = $new_record;
            } else {
                $last_record['finish'] = now()->toTimeString();
                $is_paused = false;
            }
        } else {
            $pausas = [$new_record];
        }

        $today_attendance->update([
            'pausas' => $pausas
        ]);

        return $is_paused;
    }

    public function getWeekTime()
    {
        if (is_null($this->employee_properties)) return 0;

        $payroll = Payroll::getCurrent();
        $processed_attendances = collect($payroll->getProcessedAttendances($this->id));

        $week_time = $processed_attendances->sum(fn ($item) => $item?->totalWorkedTime()['hours'] ?? 0);

        $hours = intval($week_time);
        $minutes = intval(abs($hours - $week_time) * 60);

        return [
            'formatted' => "{$hours}h {$minutes}m",
            'hours' => round($week_time / 60, 2),
        ];
    }

    public function getTotalEstimatedTime()
    {
        $productions = $this->productions;
        if ($productions->count()) {
            $totalEstimatedTime = $productions->sum(function ($production) {
                return ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
            });

            return $totalEstimatedTime;
        }

        return 0;
    }

    public function updateVacationProperties()
    {
        $update_vacations = false;
        $employee_properties = $this->employee_properties;
        // Verificar si existe algo en 'updated_date'
        if (is_null($employee_properties['vacations']['updated_date'])) {
            $employee_properties['vacations']['updated_date'] = now()->toDateString();
            $update_vacations = true;
        } else {
            // Obtener la fecha almacenada y calcular la diferencia con la fecha actual
            $lastUpdateDate = Carbon::parse($employee_properties['vacations']['updated_date']);
            $currentDate = now();
            $daysSinceLastUpdate = $currentDate->diffInDays($lastUpdateDate);

            // Verificar si ha pasado al menos una semana (7 días)
            if ($daysSinceLastUpdate >= 7) {
                $update_vacations = true;
                // sumar 7 días a la fecha anterior
                $employee_properties['vacations']['updated_date'] = Carbon::parse($employee_properties['vacations']['updated_date'])
                    ->addDays(7)
                    ->toDateString();
            }
        }

        if ($update_vacations) {
            // Calcular el proporcional por semana correspondiente según la antigüedad
            $joinDate = Carbon::parse($this->employee_properties['join_date']);
            $currentDate = now();
            $yearsOfWorking = $currentDate->diffInYears($joinDate);
            $daysToAdd = 0;

            if ($yearsOfWorking >= 0 && $yearsOfWorking < 1) {
                $daysToAdd = 12 / 52;
            } elseif ($yearsOfWorking >= 1 && $yearsOfWorking < 2) {
                $daysToAdd = 14 / 52;
            } elseif ($yearsOfWorking >= 2 && $yearsOfWorking < 3) {
                $daysToAdd = 16 / 52;
            } elseif ($yearsOfWorking >= 3 && $yearsOfWorking < 4) {
                $daysToAdd = 18 / 52;
            } elseif ($yearsOfWorking >= 4 && $yearsOfWorking < 5) {
                $daysToAdd = 20 / 52;
            } elseif ($yearsOfWorking >= 5 && $yearsOfWorking < 10) {
                $daysToAdd = 22 / 52;
            } elseif ($yearsOfWorking >= 10 && $yearsOfWorking < 15) {
                $daysToAdd = 24 / 52;
            } elseif ($yearsOfWorking >= 15 && $yearsOfWorking < 20) {
                $daysToAdd = 26 / 52;
            }

            // Acumular el proporcional de días a 'vacations'
            $employee_properties['vacations']['available_days'] += $daysToAdd;

            // Guardar los cambios en la base de datos
            $this->update(['employee_properties' => $employee_properties]);
        }
    }
}
