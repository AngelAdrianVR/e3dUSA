<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Http\Resources\PayrollUserResource;
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
        $payroll_user = PayrollUser::firstOrCreate(['id' => $request->payroll_user_id]);
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

    public function getProcessedAttendances(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll_id);
        $attendances = PayrollUserResource::collection(PayrollUser::where('user_id', $request->user_id)
            ->where('payroll_id', $request->payroll_id)
            ->oldest('date')
            ->get());
        $user = User::find($request->user_id);

        $processed = [];
        for ($i = 0; $i < 7; $i++) {
            $current_date = $payroll->start_date->addDays($i);
            $current = $attendances->firstWhere('date', $current_date);
            if ($current) {
                $processed[] = $current;
            } else {
                $payroll_user = new PayrollUser(['date' => $current_date->toDateString()]);
                if ($user->employee_properties['work_days'][$current_date->dayOfWeek]['check_in'] == 0) {
                    $payroll_user->justification_event_id = 6;
                } else {
                    $payroll_user->justification_event_id = 5;
                }
                $processed[] = PayrollUserResource::make($payroll_user);
            }
        }

        return response()->json(['items' => $processed]);
    }
}
