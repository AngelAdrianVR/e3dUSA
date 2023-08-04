<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    
    public function index()
    {
        $meetings = MeetingResource::collection(Meeting::with('user', 'users')->latest()->get()); //Traer solo reuniones agendadas por el usuario logueado.

        return inertia('Meeting/Index', compact('meetings'));
    }

    
    public function create()
    {
        $users = User::where('is_active', 1)->get();
        $meetings = Meeting::all();

        // return $meetings;

        return inertia('Meeting/Create', compact('users', 'meetings'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'location' => 'required|string',
            'url' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'required|date|after:today',
            'start' => 'required|string',
            'end' => 'required|string',
            'participants' => 'array|min:0',
        ]);

        // return $request;

        $meeting = Meeting::create($validated + [
            'user_id' => auth()->id()
        ]);

        if ($validated['participants']) {
            $meeting->users()->sync($validated['participants']);
        }

        // foreach ($validated['participants'] as $participant_id) {
        //     $meeting->users()->attach()
        // }

        return to_route('meetings.index');
    }

    
    public function show(Meeting $meeting)
    {
        //
    }

    
    public function edit(Meeting $meeting)
    {
        $users = User::all();

        return inertia('Meeting/Edit', compact('meeting', 'users'));
    }

    
    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'location' => 'required|string',
            'url' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'required|date|after:today',
            'start' => 'required',
            'end' => 'required',
            'participants' => 'required',
        ]);

        $meeting->update($validated + [
            'user_id' => auth()->id()
        ]);

        return to_route('meetings.index');
    }

    
    public function destroy(Meeting $meeting)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->meetings as $meeting) {
            $meeting = Meeting::find($meeting['id']);
            $meeting?->delete();
        }

        return response()->json(['message' => 'reunion(es) eliminada(s)']);
    }
}
