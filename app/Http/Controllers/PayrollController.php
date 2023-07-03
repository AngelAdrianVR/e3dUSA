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

        return inertia('Payroll/Show', compact('payroll', 'users', 'payrolls', 'justifications'));
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

    public function getProcessedAttendances(Request $request)
    {
        $payroll = Payroll::find($request->payroll_id);
        $attendances = PayrollUserResource::collection(PayrollUser::where('user_id', $request->user_id)
            ->where('payroll_id', $request->payroll_id)
            ->oldest('date')
            ->get());
        $user = User::find($request->user_id);

        $processed = [];
        for ($i=0; $i < 7; $i++) { 
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
