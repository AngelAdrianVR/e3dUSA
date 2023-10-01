<?php

namespace App\Http\Controllers;

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
