<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\MoreAdditionalTimeResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Payroll;
use App\Models\PayrollUser;
use App\Models\User;
use Illuminate\Http\Request;

class AdditionalTimeRequestController extends Controller
{
    
    public function index()
    {
        $more_additional_times = MoreAdditionalTimeResource::collection(AdditionalTimeRequest::where('user_id', auth()->id())->latest()->get()); //que salgan solo las solicitudes del usuario autenticado
        $payrolls_user = auth()->user()->payrolls()->wherePivot('payroll_id', Payroll::getCurrent()->id)->get();
        $requested_payrolls_user = $payrolls_user->filter(function ($payroll) {
            return !$payroll->pivot->additionalTimeRequest()->doesntExist();
        })->values();

        return inertia('AdditionalTime/Index', compact('more_additional_times', 'payrolls_user', 'requested_payrolls_user'));
    }

    public function adminIndex()
    {
        $admin_additional_times = MoreAdditionalTimeResource::collection(AdditionalTimeRequest::with('user', 'payroll')->latest()->get());
        $payrolls = Payroll::latest()->get();
        $users = User::all();
        
        return inertia('AdditionalTime/AdminIndex', compact('admin_additional_times', 'payrolls', 'users'));
    }

    public function authorizeRequest(AdditionalTimeRequest $admin_additional_time)
    {
        $admin_additional_time->authorized_at = now();
        $admin_additional_time->authorized_user_name = auth()->user()->name;
        $admin_additional_time->save();

        return to_route('admin-additional-times.index');
    }

    public function unauthorizeRequest(AdditionalTimeRequest $admin_additional_time)
    {
        $admin_additional_time->authorized_at = null;
        $admin_additional_time->authorized_user_name = null;
        $admin_additional_time->save();

        return to_route('admin-additional-times.index');
    }

    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0',
            'minutes' => 'required|numeric|min:0|max:59',
            'justification' => 'required|string',
            'payroll_id' => $request->payroll_id ? 'required' : 'nullable', //quitar*
            'user_id' => $request->user_id ? 'required' : 'nullable', //quitar*
            'payroll_user_id' => 'required|numeric|min:0',
        ]);

       $additional_time = AdditionalTimeRequest::create([
            'time_requested' => $request->hours . ':' . $request->minutes,
            'justification' => $request->justification,
            'user_id' => $request->user_id ? $request->user_id : auth()->id(), //quitar*
            'payroll_id' => $request->payroll_id ? $request->payroll_id : Payroll::getCurrent()->id, //quitar*
            'payroll_user_id' => $request->payroll_user_id,
        ]);

        event(new RecordCreated($additional_time));
    }

    public function show(AdditionalTimeRequest $additionalTimeRequest)
    {
        //
    }

    public function edit(AdditionalTimeRequest $additionalTimeRequest)
    {
        //
    }
    
    public function update(Request $request, AdditionalTimeRequest $more_additional_time)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0',
            'minutes' => 'required|numeric|min:0|max:59',
            'justification' => 'required|string',
            'payroll_user_id' => 'required|numeric|min:0',
        ]);

        $more_additional_time->update([
            'time_requested' => $request->hours . ':' . $request->minutes,
            'justification' => $request->justification,
            'user_id' => $request->user_id ? $request->user_id : auth()->id(),
            'payroll_id' => $request->payroll_id ? $request->payroll_id : Payroll::getCurrent()->id,
            'payroll_user_id' => $request->payroll_user_id,
        ]);

        event(new RecordEdited($more_additional_time));
    }

    public function destroy(AdditionalTimeRequest $additionalTimeRequest)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->more_additional_times as $more_additional_time) {
            $more_additional_time = AdditionalTimeRequest::find($more_additional_time['id']);
            $more_additional_time?->delete();

            event(new RecordDeleted($more_additional_time));
        }

        return response()->json(['message' => 'Solicitud(es) eliminada(s)']);
    }

    public function massiveDeleteAdmin(Request $request)
    {
        foreach ($request->admin_additional_times as $admin_additional_time) {
            $admin_additional_time = AdditionalTimeRequest::find($admin_additional_time['id']);
            $admin_additional_time?->delete();

            event(new RecordDeleted($admin_additional_time));
        }

        return response()->json(['message' => 'Solicitud(es) eliminada(s)']);
    }
}
