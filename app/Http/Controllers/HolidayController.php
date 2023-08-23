<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = HolidayResource::collection(Holiday::all());

        return inertia('Holiday/Index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'day' => 'required|numeric|min:1',
            'month' => 'required|numeric|min:1',
        ]);

        $holiday = Holiday::create([
            'name' => $request->name,
            'date' => "2023-$request->month-$request->day",
            'is_active' => $request->is_active,
        ]);

        event(new RecordCreated($holiday));

        return to_route('holidays.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'day' => 'required|numeric|min:1',
            'month' => 'required|numeric|min:1',
        ]);

        $holiday->update([
            'name' => $request->name,
            'date' => "2023-$request->month-$request->day",
            'is_active' => $request->is_active,
        ]);

        event(new RecordEdited($holiday));

        return to_route('holidays.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        //
    }

    // other methods
    public function massiveDelete(Request $request)
    {
        foreach ($request->holidays as $holiday) {
            $holiday = Holiday::find($holiday['id']);
            $holiday?->delete();
        }

        return response()->json(['message' => 'Dia(s) eliminado(s)']);
    }
}
