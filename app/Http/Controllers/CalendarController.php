<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    
    public function index()
    {
        $my_id = auth()->id();

        $tasks = Calendar::where('user_id', $my_id)->get();


        // return $tasks;

        return inertia('Calendar/Index', compact('tasks'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        //
    }

    
    public function edit(Calendar $calendar)
    {
        //
    }

    
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    
    public function destroy(Calendar $calendar)
    {
        //
    }
}
