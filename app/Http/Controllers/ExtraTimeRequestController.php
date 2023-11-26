<?php

namespace App\Http\Controllers;

use App\Models\ExtraTimeRequest;
use Illuminate\Http\Request;

class ExtraTimeRequestController extends Controller
{
    public function index()
    {
        $requests = ExtraTimeRequest::with('operator')->whereDate('date', '>=', today())->get();

        return response()->json(['items' => $requests]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'operator_id' => 'required|numeric|min:4',
            'hours' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        ExtraTimeRequest::create($request->all() + ['user_id' => auth()->id()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExtraTimeRequest $extraTimeRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExtraTimeRequest $extraTimeRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExtraTimeRequest $extraTimeRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExtraTimeRequest $extraTimeRequest)
    {
        //
    }

    public function setResponse(Request $request, ExtraTimeRequest $etr)
    {
        $etr->update([
            'is_accepted' => $request->is_accepted,
        ]);
    }
}
