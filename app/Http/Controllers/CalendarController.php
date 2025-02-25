<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use App\Models\User;
use App\Notifications\EventInvitationNotification;
use App\Notifications\EventInvitationResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarController extends Controller
{

    public function index()
    {
        $my_id = auth()->id();
        $tasks = CalendarResource::collection(Calendar::where('user_id', $my_id)->get());
        $pendent_invitations = Calendar::with(['user'])->where('participants', 'like', '%"user_id":' . auth()->id() . ',"status":"Pendiente"%')
            ->get();

        return inertia('Calendar/Index', compact('tasks', 'pendent_invitations'));
    }


    public function create()
    {
        $users = User::where('is_active', true)->where('id', '!=', auth()->id())->whereNot('id', 1)->get();

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

        $participants = [];
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

        // actualizar bandera de aviso invasivo de recordatorio ------------------------------
        $today = now()->toDateString();
        $dateOneDayAhead = now()->addDays(1)->toDateString();

        // Obtener recordatorios entre hoy y 1 día adelante o pasados con estado "Pendiente"
        $reminders = Calendar::where(function ($query) use ($today, $dateOneDayAhead) {
                $query->whereBetween('start_date', [$today, $dateOneDayAhead])
                    ->orWhere('start_date', '<', $today);
            })
            ->where('title', 'like', 'Cambiar precio%')
            ->where('status', 'Pendiente')
            ->get();

        // Variable para rastrear si se encontró al menos un recordatorio importante
        $hasImportantReminder = $reminders->isNotEmpty();

        // Actualizar la propiedad `has_important_reminder` en la tabla `users`
        User::query()->update(['has_important_reminder' => $hasImportantReminder]);

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
        // Actualiza el estado del evento a "Terminada"
        $calendar->update([
            'status' => 'Terminada'
        ]);

        $today = now()->toDateString();
        $dateOneDayAhead = now()->addDays(1)->toDateString();

        // Obtener recordatorios entre hoy y 1 día adelante o pasados con estado "Pendiente"
        $reminders = Calendar::where(function ($query) use ($today, $dateOneDayAhead) {
                $query->whereBetween('start_date', [$today, $dateOneDayAhead])
                    ->orWhere('start_date', '<', $today);
            })
            ->where('title', 'like', 'Cambiar precio%')
            ->where('status', 'Pendiente')
            ->get();

        // Variable para rastrear si se encontró al menos un recordatorio importante
        $hasImportantReminder = $reminders->isNotEmpty();

        // Actualizar la propiedad `has_important_reminder` en la tabla `users`
        User::query()->update(['has_important_reminder' => $hasImportantReminder]);
    }

    public function setAttendanceConfirmation(Calendar $calendar, Request $request)
    {
        // cambiar status de invitación del usuario
        $participants = $calendar->participants;
        foreach ($participants as $key => $participant) {
            if ($participant['user_id'] == auth()->id()) {
                $participants[$key]['status'] = $request->status;
            }
        }

        // actualizar participantes de registro original
        $calendar->update(['participants' => $participants]);

        // crear registro en calendario del invitado si confirmó
        if ($request->status == 'Confirmado') {
            $clone = $calendar->replicate()->fill([
                'user_id' => auth()->id(),
            ]);
            $clone->save();
        }

        // notificar a creador de evento
        $calendar->user->notify(new EventInvitationResponseNotification($calendar, $request->status, auth()->user()));

        return response()->json(['item' => isset($clone) ? $clone : null]);
    }
}
