<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = RoleResource::collection(Role::all());
        $permissions = PermissionResource::collection(Permission::all()->groupBy(function($data){
            return $data->guard_name;
        }));

        return inertia('RolePermission/Index', compact('roles', 'permissions'));
    }
}
