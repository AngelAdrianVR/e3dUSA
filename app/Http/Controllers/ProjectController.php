<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\ProjectGroupResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TagResource;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectGroup;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $projects = ProjectResource::collection(Project::with('tasks')->whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })->latest()->paginate(30));

        return inertia('Project/Index', compact('projects'));
    }

    public function dashboard()
    {
        return inertia('Project/Dashboard');
    }

    public function create()
    {
        $companies = Company::with('companyBranches.sales')->latest()->get();
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $users = User::where('is_active', true)->whereNot('id', 1)->get();

        return inertia('Project/Create', compact('companies', 'users', 'tags', 'project_groups'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_group_id' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'currency' => 'nullable|string',
            'sat_method' => 'nullable|string',
            'description' => 'nullable',
            'is_strict_project' => 'boolean',
            'is_internal_project' => 'boolean',
            'budget' => 'nullable|numeric|min:0|max:999999.99',
            'selectedUsersToPermissions' => 'array|min:1',
            // 'type_access_project' => 'required|string',
            'start_date' => 'required',
            'limit_date' => 'required',
            'owner_id' => 'required|numeric|min:1',
            'company_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'company_branch_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'sale_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
        ]);

        $project = Project::create($validated + ['user_id' => auth()->id()]);
        event(new RecordCreated($project));

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->attach($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('projects.show', $project);
    }

    public function show($project_id)
    {
        $project = ProjectResource::make(Project::with(['tasks' => ['participants', 'project', 'user'], 'owner', 'user', 'tags'])->find($project_id));
        $projects = ProjectResource::collection(Project::with(['tasks' => ['participants', 'project', 'user', 'comments.user', 'media'], 'user', 'users', 'company', 'owner', 'tags'])->latest()->get());
        $users = User::all();

        $defaultTab = request('defaultTab');

        return inertia('Project/Show', compact('project', 'projects', 'users', 'defaultTab'));
    }


    public function edit($project_id)
    {
        $project = ProjectResource::make(Project::with(['tags', 'company', 'owner', 'users'])->find($project_id));
        $companies = Company::with('companyBranches.sales')->latest()->get();
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $users = User::where('is_active', true)->whereNot('id', 1)->get();
        $media = $project->getMedia()->all();

        // return $project;
        return inertia('Project/Edit', compact('companies', 'users', 'tags', 'project_groups', 'project', 'media'));
    }


    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_group_id' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'currency' => 'nullable|string',
            'sat_method' => 'nullable|string',
            'description' => 'nullable',
            'is_strict_project' => 'boolean',
            'is_internal_project' => 'boolean',
            'budget' => 'nullable|numeric|min:0|max:999999.99',
            'selectedUsersToPermissions' => 'array|min:1',
            // 'type_access_project' => 'required|string',
            'start_date' => 'required',
            'limit_date' => 'required',
            'owner_id' => 'required|numeric|min:1',
            'company_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'company_branch_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'sale_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
        ]);


        $project->update($validated);
        event(new RecordEdited($project));

        // permisos
        // Eliminar todos los permisos actuales para el proyecto
        $project->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->sync($tagIds);

        return to_route('projects.show', $project);
        //event(new RecordEdited($project));
    }

    public function updateWithMedia(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_group_id' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'currency' => 'nullable|string',
            'sat_method' => 'nullable|string',
            'description' => 'nullable',
            'is_strict_project' => 'boolean',
            'is_internal_project' => 'boolean',
            'budget' => 'nullable|numeric|min:0|max:999999.99',
            'selectedUsersToPermissions' => 'array|min:1',
            'start_date' => 'required',
            'limit_date' => 'required',
            'owner_id' => 'required|numeric|min:1',
            'company_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'company_branch_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
            'sale_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->is_internal_project;
            })],
        ]);


        $project->update($validated);
        event(new RecordEdited($project));

        // permisos
        // Eliminar todos los permisos actuales para el proyecto
        $project->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->sync($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('projects.show', $project);
    }


    public function destroy(Project $project)
    {
        $project->delete();
        event(new RecordDeleted($project));
    }
}
