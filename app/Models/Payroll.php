<?php

namespace App\Models;

use App\Http\Resources\PayrollUserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'week',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date'
    ];

    //Rellationships
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(PayrollUser::class)
            ->withPivot([
                'id',
                'date',
                'check_in',
                'start_break',
                'end_break',
                'check_out',
                'late',
                'extras_enabled',
                'extra_hours',
                'extra_minutes',
                'additionals',
            ])
            ->withTimestamps();
    }


    public static function getCurrent()
    {
        return self::orderBy('id', 'desc')->first();

    // methods
    public function getProcessedAttendances($user_id)
    {
        $attendances = PayrollUser::where('user_id', $user_id)
            ->where('payroll_id', $this->id)
            ->oldest('date')
            ->get();
        $user = User::find($user_id);

        $processed = [];
        for ($i = 0; $i < 7; $i++) {
            $current_date = $this->start_date->addDays($i);
            $current = $attendances->firstWhere('date', $current_date);
            if ($current) {
                $processed[] = $current;
            } else {
                $payroll_user = new PayrollUser(['date' => $current_date->toDateString()]);
                if ($current_date->greaterThan(now())) {
                    $payroll_user->justification_event_id = 7;
                } else {
                    if ($user->employee_properties['work_days'][$current_date->dayOfWeek]['check_in'] == 0) {
                        $payroll_user->justification_event_id = 6;
                    } else {
                        $payroll_user->justification_event_id = 5;
                    }
                }
                $processed[] = $payroll_user;
            }
        }

        return $processed;

    }
}
