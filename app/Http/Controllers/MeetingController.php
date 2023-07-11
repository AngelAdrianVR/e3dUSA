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
        $meetings = MeetingResource::collection(Meeting::with('user')->latest()->get()); //Traer solo reuniones agendadas por el usuario logueado.
        return inertia('Meeting/Index', compact('meetings'));
    }

    
    public function create()
    {
        $users = User::all();

        return inertia('Meeting/Create', compact('users'));
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Meeting $meeting)
    {
        //
    }

    
    public function edit(Meeting $meeting)
    {
        //
    }

    
    public function update(Request $request, Meeting $meeting)
    {
        //
    }

    
    public function destroy(Meeting $meeting)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->companies as $company) {
            $company = Company::find($company['id']);
            $company?->delete();
        }

        return response()->json(['message' => 'Cliente(s) eliminado(s)']);
    }
}
