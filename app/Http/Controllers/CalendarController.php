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
        return inertia('Calendar/Create');
    }

    
    public function store(Request $request)
    {
        //
    }

    
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
        $calendar->delete();
    }

    public function taskDone(Calendar $calendar)
    {
        $calendar->update([
            'status' => 'Terminada'
        ]);
    }
}
