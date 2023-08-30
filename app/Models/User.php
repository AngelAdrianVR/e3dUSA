<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'employee_properties',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'employee_properties' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    //Relationships
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
            // $discount = Discount::find($discount_id);
            $discounts[] = [
                'id' => $discount_id,
                'amount' => $discount_id == 1 ? 100 : 32
            ];
        }

        $today_attendance = PayrollUser::firstOrCreate(['date' => today()->toDateString(), 'user_id' => $this->id], [
            'payroll_id' => Payroll::getCurrent()->id,
            'additionals' => [
                'salary' =>  $this->employee_properties['salary'],
                'bonuses' => $bonuses,
                'discounts' => $discounts,
                'hours_per_week' => $this->employee_properties['hours_per_week'],
            ],
        ]);

        $today_attendance->update(['late' => $today_attendance->getLateTime()]);

        $now_time = now()->isoFormat('HH:mm');

        if (is_null($today_attendance->check_in)) {
            $today_attendance->update([
                'check_in' => $now_time,
                'late' => $today_attendance->getLateTime(),
            ]);
            $next = 'Registrar inicio break';
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
}
