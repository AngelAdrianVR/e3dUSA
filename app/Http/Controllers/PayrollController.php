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

        $processed = [];
        for ($i=0; $i < 7; $i++) { 
            $current = $attendances->firstWhere('date', $payroll->start_date->addDays($i));
            if ($current) {
                $processed[] = $current;
            } else {
                $processed[] = 'FALTA';
            }
        }

        return response()->json(['items' => $processed]);
    }
}
