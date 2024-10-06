<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function index()
    {   
        $boxes = Box::all();

        return inertia('Box/Index', compact('boxes'));
    }

    public function create()
    {
        return inertia('Box/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'length' => 'required|numeric|min:0|max:999',
            'height' => 'required|numeric|min:0|max:999',
            'width' => 'required|numeric|min:0|max:999',
        ]);

        Box::create($request->all());
    }

    public function show(Box $box)
    {
        //
    }

    public function edit(Box $box)
    {
        return inertia('Box/Edit', compact('box'));
    }

    public function update(Request $request, Box $box)
    {
        $request->validate([
            'name' => 'required|string',
            'length' => 'required|numeric|min:0|max:999',
            'height' => 'required|numeric|min:0|max:999',
            'width' => 'required|numeric|min:0|max:999',
        ]);

        $box->update($request->all());

        return to_route('boxes.index');
    }

    public function destroy(Box $box)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->boxes as $box) {
            $box = Box::find($box['id']);
            $box?->delete();

            event(new RecordDeleted($box));
        }

        return to_route('boxes.index');
    }
}
