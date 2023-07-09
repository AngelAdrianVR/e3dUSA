<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoreAdditionalTimeResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Payroll;
use Illuminate\Http\Request;

class AdditionalTimeRequestController extends Controller
{
    
    public function index()
    {
        $more_additional_times = MoreAdditionalTimeResource::collection(AdditionalTimeRequest::all());
        // return $more_additional_times;
        return inertia('AdditionalTime/Index', compact('more_additional_times'));
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
        ]);

        AdditionalTimeRequest::create([
            'time_requested' => $request->hours . ':' . $request->minutes,
            'justification' => $request->justification,
            'user_id' => auth()->id(),
            'payroll_id' => Payroll::getCurrent()->id,
        ]);

        return to_route('more-additional-times.index');
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
        ]);

        $more_additional_time->update([
            'time_requested' => $request->hours . ':' . $request->minutes,
            'justification' => $request->justification,
            'user_id' => auth()->id(),
            'payroll_id' => Payroll::getCurrent()->id,
        ]);

        return to_route('more-additional-times.index');
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
        }

        return response()->json(['message' => 'Solicitud(es) eliminada(s)']);
    }
}
