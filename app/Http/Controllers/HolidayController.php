<?php

namespace App\Http\Controllers;

use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        //
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
        //
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
