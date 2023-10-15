<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditResource;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    
    public function index()
    {
        $audits_created = AuditResource::collection(Audit::where('action', 'Creación')->latest()->paginate(50));
        $audits_edited = AuditResource::collection(Audit::where('action', 'Edición')->latest()->paginate(50));
        $audits_deleted = AuditResource::collection(Audit::where('action', 'Eliminación')->latest()->paginate(50));

        $users = User::all();

        // return $audits_created;
        return inertia('ActionHistory/Index', compact('audits_created', 'audits_edited', 'audits_deleted', 'users'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Audit $audit)
    {
        //
    }

    
    public function edit(Audit $audit)
    {
        //
    }

    
    public function update(Request $request, Audit $audit)
    {
        //
    }

    
    public function destroy(Audit $audit)
    {
        //
    }
}
