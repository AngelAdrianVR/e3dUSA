<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Models\ProjectGroup;
use Illuminate\Http\Request;

class ProjectGroupController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = ProjectGroup::create($request->all());

        event(new RecordCreated($group));

        return response()->json(['message' => 'Se ha creado un nuevo grupo', 'item' => $group]);
    }

    public function show(ProjectGroup $projectGroup)
    {
        //
    }

    public function edit(ProjectGroup $projectGroup)
    {
        //
    }

    public function update(Request $request, ProjectGroup $projectGroup)
    {
        //
    }

    public function destroy(ProjectGroup $projectGroup)
    {
        //
    }
}
