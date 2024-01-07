<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Http\Resources\PayrollUserResource;
use App\Http\Resources\UserResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Bonus;
use App\Models\Discount;
use App\Models\ExtraTimeRequest;
use App\Models\JustificationEvent;
use App\Models\Payroll;
use App\Models\PayrollUser;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{

    public function index()
    {
        $payrolls = PayrollResource::collection(Payroll::latest()->get());

        return inertia('Payroll/Index', compact('payrolls'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($payroll_id)
    {
        $justifications = JustificationEvent::all();
        $users = UserResource::collection(User::whereHas('payrolls', function ($query) use ($payroll_id) {
            $query->where('payrolls.id', $payroll_id);
        })->get());
        $payroll_users = PayrollUser::where('payroll_id', $payroll_id)->get(['user_id', 'id'])->groupBy('user_id');
        $payroll = PayrollResource::make(Payroll::with('users')->find($payroll_id));

        //recupera todos los payrolls pero solo id y numero de semana para optimizar la carga de la vista.
        $pre_payrolls = PayrollResource::collection(Payroll::latest()->get());
        $payrolls = $pre_payrolls->map(function ($payroll) {
            return [
                'id' => $payroll->id,
                'week' => $payroll->week,
            ];
        });

        // return $payrolls;
        return inertia('Payroll/Show', compact('payroll', 'users', 'payrolls', 'justifications', 'payroll_users'));
    }


    public function edit(Payroll $payroll)
    {
        //
    }


    public function update(Request $request, Payroll $payroll)
    {
        //
    }


    public function destroy(Payroll $payroll)
    {
        //
    }

    public function printTemplate($users_id_to_show, $payroll_id)
    {
        $users = User::all();
        $users_id_to_show = json_decode($users_id_to_show);

        return inertia('Payroll/PrintTemplate', compact('users', 'payroll_id', 'users_id_to_show'));
    }


    public function handleLate(Request $request)
    {
        $payroll_user = PayrollUser::find($request->payroll_user_id);
        if ($payroll_user->late) {
            $payroll_user->late = 0;
        } else {
            $payroll_user->late = $payroll_user->getLateTime();
        }

        $payroll_user->save();

        return response()->json(['late' => $payroll_user->late]);
    }

    public function handleExtras(Request $request)
    {
        $payroll_user = PayrollUser::find($request->payroll_user_id);
        if ($payroll_user->extras_enabled)
            $payroll_user->extras_enabled = 0;
        else
            $payroll_user->extras_enabled = 1;

        $payroll_user->save();

        return response()->json(['extras_enabled' => $payroll_user->extras_enabled, 'extras' => $payroll_user->getExtras()]);
    }

    public function handleIncidents(Request $request)
    {
        $payroll_user = PayrollUser::firstOrCreate(['id' => $request->payroll_user_id], [
            'date' => $request->date,
            'user_id' => $request->user_id,
            'payroll_id' => $request->payroll_id,
        ]);
        $user = User::find($request->user_id);
        $ep = $user->employee_properties;

        if ($request->incident_id == 3) { //vacations?
            if ($ep['vacations']['available_days'] >= 1) {
                $ep['vacations']['available_days'] = $ep['vacations']['available_days'] - 1;
                $user->update(['employee_properties' => $ep]);

                $payroll_user->justification_event_id = $request->incident_id;
                $payroll_user->save();
                return response()->json(['message' => 'Incidencia cambiada', 'title' => 'Éxito', 'type' => 'success', 'item' => PayrollUserResource::make($payroll_user)]);
            } else {
                return response()->json(['message' => 'El colaborador no tiene vacaiones disponibles suficientes', 'title' => 'Error', 'type' => 'error', 'item' => PayrollUserResource::make($payroll_user)]);
            }
        } else {
            $payroll_user->justification_event_id = $request->incident_id;
            $payroll_user->save();
            return response()->json(['message' => 'Incidencia cambiada', 'title' => 'Éxito', 'type' => 'success', 'item' => PayrollUserResource::make($payroll_user)]);
        }
    }

    public function handleAttendance(Request $request)
    {
        $payroll_user = PayrollUser::firstOrCreate(['id' => $request->payroll_user_id], [
            'date' => $request->date,
            'user_id' => $request->user_id,
            'payroll_id' => $request->payroll_id,
        ]);
        $payroll_user->justification_event_id = null;

        $payroll_user->save();

        return response()->json(['item' => PayrollUserResource::make($payroll_user)]);
    }

    public function updateAttendances(Request $request)
    {
        $user = User::find($request->user_id);
        $bonuses = [];
        $discounts = [];
        foreach ($user->employee_properties['bonuses'] as $bonus_id) {
            $bonus = Bonus::find($bonus_id);
            $bonuses[] = [
                'id' => $bonus_id,
                'amount' => $user->employee_properties['hours_per_week'] >= 48
                    ? $bonus->full_time
                    : $bonus->half_time
            ];
        }
        foreach ($user->employee_properties['discounts'] as $discount_id) {
            $discount = Discount::find($discount_id);
            $discounts[] = [
                'id' => $discount_id,
                'amount' => $discount->amount
            ];
        }

        foreach ($request->attendances as $attendance) {
            $payroll_user = PayrollUser::find($attendance['id']);

            if ($payroll_user) {
                $day_of_week = $payroll_user->date->dayOfWeek;
                $payroll_user->update([
                    'check_in' => $attendance['check_in'],
                    'check_out' => $attendance['check_out'],
                    'pausas' => $attendance['pausas'],
                    'additionals' => [
                        'salary' =>  [
                            "week" => $user->employee_properties['salary']['week'],
                            "day" => $user->employee_properties['work_days'][$day_of_week]["salary"],
                            "hour" => $user->employee_properties['salary']['hour'],
                        ],
                        'bonuses' => $bonuses,
                        'discounts' => $discounts,
                        'hours_per_week' => $user->employee_properties['hours_per_week'],
                    ],
                ]);

                $payroll_user->late = $payroll_user->getLateTime();
                $payroll_user->save();
            }
        }

        return response()->json(['message' => 'Asistencias actualizadas']);
    }

    public function getPayroll(Request $request)
    {
        $payroll = PayrollResource::make(Payroll::find($request->payroll_id));

        return response()->json(['item' => $payroll]);
    }

    public function getPayrollUsers(Request $request)
    {
        $payroll_id = $request->payroll_id;
        $users = UserResource::collection(User::whereHas('payrolls', function ($query) use ($payroll_id) {
            $query->where('payrolls.id', $payroll_id);
        })->get());
        $payroll_users = PayrollUser::where('payroll_id', $payroll_id)->get(['user_id', 'id'])->groupBy('user_id');

        return response()->json(compact('users', 'payroll_users'));
    }

    public function getBonuses(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $processed = collect($payroll->getProcessedAttendances($request->user_id));
        $user = User::find($request->user_id);

        // bonuses
        $bonuses = [];
        $user_bonuses = $user->employee_properties['bonuses'];
        foreach ($user_bonuses as $user_bonus_id) {
            $current_bonus = Bonus::find($user_bonus_id);
            $amount = $user->employee_properties['hours_per_week'] >= 48
                ? $current_bonus->full_time
                : $current_bonus->half_time;

            if ($user_bonus_id === 1) { // Asistencia
                $absent = $processed->first(fn ($item) => $item->justification_event_id === 5);
                if ($absent) {
                    $amount = 0;
                }
            } elseif ($user_bonus_id === 2) { //Puntualidad
                $days_late = $processed->filter(fn ($item) => $item->late)->count();
                $absents = $processed->filter(fn ($item) => $item->justification_event_id === 5)->count();
                $discount = $amount / 6;
                $amount -= ($days_late + $absents) * $discount;
            } elseif ($user_bonus_id === 3) { //productividad
                $absents = $processed->filter(fn ($item) => $item->justification_event_id === 5)->count();
                $discount = $amount / 6;
                $amount -= $absents * $discount;
            } elseif ($user_bonus_id === 4) { //Prima dominical
                if (is_null($processed[2]->check_out)) {
                    $amount = 0;
                }
            } elseif ($user_bonus_id === 5) { //Puntualidad jefe produccion
                $days_late = $processed->filter(fn ($item) => $item->late)->count();
                $absents = $processed->filter(fn ($item) => $item->justification_event_id === 5)->count();
                $discount = $amount / 6;
                $amount -= ($days_late + $absents) * $discount;
            }

            $bonuses[] = ['name' => $current_bonus->name, 'amount' => ['number_format' => number_format($amount, 2), 'raw' => $amount]];
        }


        return response()->json(['item' => $bonuses]);
    }

    public function getDiscounts(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $processed = collect($payroll->getProcessedAttendances($request->user_id));
        $user = User::find($request->user_id);

        // discounts
        $discounts = [];
        $user_discounts = $user->employee_properties['discounts'];
        foreach ($user_discounts as $user_discount_id) {
            $current_discount = Discount::find($user_discount_id);
            $amount = 0;

            if ($user_discount_id === 1) { // puntualidad
                $minutes_late = $processed->sum('late');
                if ($minutes_late >= 15) {
                    $amount = $current_discount->amount;
                }
            } elseif ($user_discount_id === 2) { //seguro
                $amount = $current_discount->amount;
            }

            $discounts[] = ['name' => $current_discount->name, 'amount' => ['number_format' => number_format($amount, 2), 'raw' => $amount]];
        }

        return response()->json(['item' => $discounts]);
    }

    public function getExtras(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $processed = collect($payroll->getProcessedAttendances($request->user_id));
        $user = User::find($request->user_id);

        $extras = $processed->sum(function ($item) {
            return $item->getExtras()['minutes'];
        });

        $amount = $processed->sum(function ($item) {
            return $item->getExtras()['amount']['raw'];
        });

        $hours = intval($extras / 60);
        $minutes = $extras % 60;

        return response()->json(['item' => ['formatted' => "{$hours}h {$minutes}m", 'minutes' => $extras, 'amount' => ['number_format' => number_format($amount, 2), 'raw' => $amount]]]);
    }

    public function getExtrasRequests(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $extra_requested = ExtraTimeRequest::whereBetween('date', [$payroll->start_date, $payroll->start_date->addDays(7)])
            ->where('operator_id', $request->user_id)
            ->where('is_accepted', true)
            ->get();

        $totalBonus = $extra_requested->sum('bonus');

        return response()->json(['item' => $totalBonus]);
    }

    public function getProcessedAttendances(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll_id);
        $processed = PayrollUserResource::collection($payroll->getProcessedAttendances($request->user_id));

        return response()->json(['items' => $processed]);
    }

    public function closeCurrent()
    {
        $current = Payroll::getCurrent();
        $new = Payroll::create([
            'start_date' => $current->start_date->addDays(7)->toDateString(),
            'week' => today()->addDays(7)->weekOfYear,
        ]);

        $current->update(['is_active' => 0]);

        return response()->json(['message' => "Nomina semana $current->week cerrada", 'item' => PayrollResource::make($new)]);
    }

    public function getCurrentPayroll()
    {
        $current = Payroll::getCurrent();

        return response()->json(['item' => $current]);
    }

    public function getAllPayrolls()
    {
        $all = Payroll::latest()->get();

        return response()->json(['items' => $all]);
    }

    public function getAdditionalTime(Request $request)
    {
        $additiona_times = AdditionalTimeRequest::where('user_id', $request->user_id)->where('payroll_id', $request->payroll_id)->whereNotNull('authorized_at')->get();
        return response()->json(['items' => $additiona_times]);
    }

    public function getUsers($payroll_id)
    {
        $payroll = Payroll::find($payroll_id);
        $users = $payroll->users->unique('name');

        return response()->json(['items' => $users]);
    }
}
