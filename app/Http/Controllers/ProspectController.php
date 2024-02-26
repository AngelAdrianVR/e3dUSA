<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProspectResource;
use App\Models\Prospect;
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
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Prospect $prospect)
    {
        //
    }

    public function edit(Prospect $prospect)
    {
        //
    }

    public function update(Request $request, Prospect $prospect)
    {
        //
    }

    public function destroy(Prospect $prospect)
    {
        //
    }

    public function getMatches($query)
    {
        if ($query != 'nullable') {
            $prospects = ProspectResource::collection(Prospect::with('seller')
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('contact_name', 'LIKE', "%$query%")
                ->orWhere('contact_phone', 'LIKE', "%$query%")
                ->orWhereHas('seller', function ($q) use ($query) {
                    $q->where('seller.name', 'LIKE', "%$query%");
                })
                ->latest()
                ->get());
        } else {
            $prospects = ProspectResource::collection(Prospect::with('seller')->latest()->paginate(20));
        }

        return response()->json(['items' => $prospects]);
    }
}
