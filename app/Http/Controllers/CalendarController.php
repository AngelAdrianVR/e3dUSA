<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Models\Calendar;
use App\Models\User;
use App\Notifications\EventInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarController extends Controller
{

    public function index()
    {
        $my_id = auth()->id();

        $tasks = Calendar::where('user_id', $my_id)->get();
        $pendent_invitations = Calendar::with(['user'])->where('participants', 'like', '%"user_id":' . auth()->id() . ',"status":"Pendiente"%')
            ->get();

        return inertia('Calendar/Index', compact('tasks', 'pendent_invitations'));
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
            'participants' => [Rule::requiredIf(function () use ($request) {
                return $request->type == 'Evento';
            })],
            'repeater' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'reminder' => 'nullable|string',
            'is_full_day' => 'boolean',
            'start_time' => [[Rule::requiredIf(function () use ($request) {
                return !$request->is_full_day;
            })]],
            'end_time' => [[Rule::requiredIf(function () use ($request) {
                return !$request->is_full_day;
            })]],
            'start_date' => 'required',
        ]);

        // Obtén los valores del array 'time' y asígnalos a las variables 'start_at' y 'finish_at'
        // list($start_at, $finish_at) = $request->input('time');

        // procesar arreglo de participantes
        foreach ($request->participants as $key => $participantId) {
            $participants[] = [
                "user_id" => $participantId,
                "status" => "Pendiente",
            ];
        }

        $calendar = Calendar::create([
            'type' => $request->type,
            'title' => $request->title,
            'repeater' => $request->repeater,
            'location' => $request->location,
            'description' => $request->description,
            'reminder' => $request->reminder,
            'is_full_day' => $request->is_full_day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_date' => $request->start_date,
            'finish_date' => $request->finish_date,
            'participants' => $participants,
            'user_id' => auth()->id(),
        ]);

        // notificar a participantes
        foreach ($request->participants as $participant_id) {
            $participant = User::find($participant_id);
            $participant->notify(new EventInvitationNotification($calendar));
        }

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
