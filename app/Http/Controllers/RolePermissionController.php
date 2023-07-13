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

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'permissions' => 'array|min:1'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json(['item' => RoleResource::make($role)]);
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array|min:1'
        ]);

        foreach ($request->permissions as $permission_id) {
            $permission = Permission::find($permission_id);
            $role->givePermissionTo($permission);
        }

        return response()->json(['item' => RoleResource::make($role)]);
    }

    public function deleteRole(Role $role)
    {
        $role->delete();

        return response()->json(['message' => "Rol: *$role->name* eliminado"]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'guard_name' => 'nullable'
        ]);

        $permission = Permission::create($request->all());

        return response()->json(['item' => PermissionResource::make($permission)]);
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'guard_name' => 'nullable'
        ]);

        $permission->update($request->all());

        return response()->json(['item' => PermissionResource::make($permission)]);
    }

    public function deletePermission(Permission $permission)
    {
        $permission->delete();

        return response()->json(['message' => "Permiso: *$permission->name* eliminado"]);
    }
}
