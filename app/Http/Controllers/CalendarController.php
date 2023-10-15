<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Models\Calendar;
use App\Models\User;
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
        $users = User::where('is_active', true)->get();
        
        return inertia('Calendar/Create', compact('users'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'participants' => 'nullable|array|min:1',
            'repeater' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'reminder' => 'nullable|string',
            'is_full_day' => 'boolean',
            'time' => 'required|array|min:2', //viene un array de hora de inicio y hora de termino
            'start_date' => 'required',
        ]);

         // Obtén los valores del array 'time' y asígnalos a las variables 'start_at' y 'finish_at'
    list($start_at, $finish_at) = $request->input('time');

      $calendar = Calendar::create([
            'type' => $request->type,
            'title' => $request->title,
            'repeater' => $request->repeater,
            'location' => $request->location,
            'description' => $request->description,
            'reminder' => $request->reminder,
            'is_full_day' => $request->is_full_day,
            'start_at' => $start_at,
            'finish_at' => $finish_at,
            'start_date' => $request->start_date,
            'finish_date' => $request->start_date,
            'user_id' => auth()->id(),
        ]);

        event(new RecordCreated($calendar));

        return to_route('calendars.index');
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
        event(new RecordDeleted($calendar));
    }

    public function taskDone(Calendar $calendar)
    {
        $calendar->update([
            'status' => 'Terminada'
        ]);
    }
}
