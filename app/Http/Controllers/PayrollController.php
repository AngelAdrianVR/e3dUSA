<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Http\Resources\PayrollUserResource;
use App\Models\Bonus;
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
        $payroll = PayrollResource::make(Payroll::find($payroll_id));
        $users = User::all();
        $payrolls = PayrollResource::collection(Payroll::with('users')->get());
        $justifications = JustificationEvent::all();
        $payroll_users = PayrollUser::where('payroll_id', $payroll_id)->get(['user_id', 'id'])->groupBy('user_id');

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

    public function printTemplate(Request $request)
    {
        $users = User::all();
        $payroll_id = $request->payroll_id;
        $users_id_to_show = $request->users_id_to_show;

        return inertia('Payroll/PrintTemplate', compact('users', 'payroll_id', 'users_id_to_show'));
    }


    public function handleLate(Request $request)
    {
        $payroll_user = PayrollUser::find($request->payroll_user_id);
        if ($payroll_user->late) {
            $payroll_user->late = 0;
        }
        else {
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

        return response()->json(['extras_enabled' => $payroll_user->extras_enabled]);
    }

    public function handleIncidents(Request $request)
    {
        $payroll_user = PayrollUser::firstOrCreate(['id' => $request->payroll_user_id],[
            'date' => $request->date,
            'user_id' => $request->user_id,
            'payroll_id' => $request->payroll_id,
        ]);
        $payroll_user->justification_event_id = $request->incident_id;

        $payroll_user->save();

        return response()->json(['item' => PayrollUserResource::make($payroll_user)]);
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

        foreach ($request->attendances as $attendance) {
            $payroll_user = PayrollUser::find($attendance['id']);

            if ($payroll_user) {
                $payroll_user->update([
                    'check_in' => $attendance['check_in'],
                    'start_break' => $attendance['start_break'],
                    'end_break' => $attendance['end_break'],
                    'check_out' => $attendance['check_out'],
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

    public function getExtras(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $processed = collect($payroll->getProcessedAttendances($request->user_id));
        $user = User::find($request->user_id);

        $extras = $processed->sum(function ($item){
            return $item->getExtras()['minutes'];
        });

        $hours = intval($extras / 60);
        $minutes = $extras % 60;

        $amount = $user->employee_properties['salary']['hour'] * ($extras / 60);

        return response()->json(['item' => ['formatted' => "{$hours}h {$minutes}m", 'minutes' => $extras, 'amount' => ['number_format' => number_format($amount, 2), 'raw' => $amount]]]);
    }

    public function getProcessedAttendances(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll_id);
        $processed = PayrollUserResource::collection($payroll->getProcessedAttendances($request->user_id));

        return response()->json(['items' => $processed]);
    }
}
