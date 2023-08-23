<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Http\Resources\BonusResource;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bonuses = BonusResource::collection(Bonus::all());

        return inertia('Bonus/Index', compact('bonuses'));
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
            'description' => 'nullable',
            'full_time' => 'required|numeric|min:1',
            'half_time' => 'required|numeric|min:1',
        ]);

        $bonus = Bonus::create($request->all());

        event(new RecordCreated($bonus));

        return to_route('bonuses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bonus $bonus)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable',
            'full_time' => 'required|numeric|min:1',
            'half_time' => 'required|numeric|min:1',
        ]);

        $bonus->update($request->all());

        return to_route('bonuses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        //
    }

    // other methods
    public function massiveDelete(Request $request)
    {
        foreach ($request->bonuses as $bonus) {
            $bonus = Bonus::find($bonus['id']);
            $bonus?->delete();
        }

        return response()->json(['message' => 'Bono(s) eliminado(s)']);
    }
}
