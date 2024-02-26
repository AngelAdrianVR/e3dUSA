<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Http\Resources\ProspectResource;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Http\Request;

class ProspectController extends Controller
{

    public function index()
    {
        $prospects = ProspectResource::collection(Prospect::paginate(20));

        return inertia('Prospect/Index', compact('prospects'));
    }

    public function create()
    {
        $sellers = User::where('employee_properties->department', 'Ventas')->get(['id', 'name', 'profile_photo_path']);

        return inertia('Prospect/Create', compact('sellers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
            'contact_charge' => 'required|string|max:255',
            'contact_phone' => 'required|string',
            'contact_phone_extension' => 'nullable|string|max:5',
            'status' => 'required|string|max:255',
            'branches_number' => 'required|numeric|min:1',
            'abstract' => 'required|string|max:800',
            'seller_id' => 'nullable|numeric|min:1',
        ]);

        $prospect = Prospect::create($validated + ['user_id' => auth()->id()]);

        return to_route('prospects.show', $prospect);
    }

    public function show(Prospect $prospect)
    {
        $prospect = ProspectResource::make($prospect->load(['seller', 'user']));
        $prospects = Prospect::latest()->get(['id', 'name']);

        $defaultTab = request('defaultTab');

        return inertia('Prospect/Show', compact('prospect', 'prospects', 'defaultTab'));
    }

    public function edit(Prospect $prospect)
    {
        $prospect = $prospect->load('seller');
        $sellers = User::where('employee_properties->department', 'Ventas')->get(['id', 'name', 'profile_photo_path']);

        return inertia('Prospect/Edit', compact('prospect', 'sellers'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
            'contact_charge' => 'required|string|max:255',
            'contact_phone' => 'required|string',
            'contact_phone_extension' => 'nullable|string|max:5',
            'status' => 'required|string|max:255',
            'branches_number' => 'required|numeric|min:1',
            'abstract' => 'required|string|max:800',
            'seller_id' => 'nullable|numeric|min:1',
        ]);

        $prospect->update($validated);

        return to_route('prospects.show', $prospect);
    }

    public function destroy(Prospect $prospect)
    {
        $prospect->delete();
        event(new RecordDeleted($prospect));
    }

    public function getMatches($query)
    {
        if ($query != 'nullable') {
            $prospects = ProspectResource::collection(Prospect::with('seller')
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('contact_name', 'LIKE', "%$query%")
                ->orWhere('contact_phone', 'LIKE', "%$query%")
                ->orWhereHas('seller', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                })
                ->latest()
                ->get());
        } else {
            $prospects = ProspectResource::collection(Prospect::with('seller')->latest()->paginate(20));
        }

        return response()->json(['items' => $prospects]);
    }
}
