<?php

namespace App\Http\Controllers;

use App\Events\RecordEdited;
use App\Models\Manual;
use Illuminate\Http\Request;

class ManualController extends Controller
{
    public function index()
    {
        $manuals = Manual::with(['media', 'user'])->get();

        return inertia('Manuals/Index', compact('manuals'));
    }

    public function create()
    {
        return inertia('Manuals/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:460',
            'cover' => 'nullable|mimetypes:image/*',
            'media' => $request->type == 'Manual'
                ? 'required|mimetypes:application/pdf'
                : 'required|mimetypes:video/*',
        ]);

        $manual = Manual::create($validated + ['user_id' => auth()->id()]);

        // Adjunta la imagen de portada
        if ($request->file('cover')) {
            $manual->addMedia($request->file('cover'))
                ->toMediaCollection('cover');
        }

        // Adjunta el archivo de manual
        $manual->addMedia($request->file('media'))
            ->toMediaCollection();

        return to_route('manuals.index');
    }

    public function show(Manual $manual)
    {
        //
    }

    public function edit(Manual $manual)
    {
        $manual = $manual->load('media');

        return inertia('Manuals/Edit', compact('manual'));
    }

    public function update(Request $request, Manual $manual)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:355',
        ]);

        $manual->update($validated);

        return to_route('manuals.index');
    }

    public function updateWithMedia(Request $request, Manual $manual)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:460',
            'cover' => 'nullable|mimetypes:image/*',
            'media' => $request->type == 'Manual'
                ? 'required|mimetypes:application/pdf'
                : 'required',
        ]);

        $manual->update($validated);

        // limpiar media
        $manual->clearMediaCollection();

        // Adjunta la imagen de portada
        if ($request->file('cover')) {
            $manual->addMedia($request->file('cover'))
                ->toMediaCollection('cover');
        }
        // Adjunta el archivo de manual
        $manual->addMedia($request->file('media'))
            ->toMediaCollection();

        event(new RecordEdited($manual));

        return to_route('manuals.index');
    }

    public function destroy(Manual $manual)
    {
        //
    }
}
