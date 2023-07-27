<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Bonus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index()
    {
        $users = UserResource::collection(User::latest()->get());

        return inertia('User/Index', compact('users'));
    }


    public function create()
    {
        $employee_number = User::orderBy('id', 'desc')->first()->id + 1;
        $bonuses = Bonus::where('is_active', 1)->get();
        $roles = Role::all();

        return inertia('User/Create', compact('employee_number', 'bonuses', 'roles'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users,email',
            'roles' => 'array|min:1',
            'employee_properties.salary.week' => 'required|numeric|min:1',
            'employee_properties.hours_per_week' => 'required|numeric|min:1',
            'employee_properties.birthdate' => 'required|date',
            'employee_properties.join_date' => 'required|date',
            'employee_properties.job_position' => 'required|string',
            'employee_properties.department' => 'required|string',
            'employee_properties.work_days' => 'array',
            'employee_properties.bonuses' => 'nullable',
            'employee_properties.vacations' => 'nullable',
        ]);
        $work_days = 0;
        foreach ($validated['employee_properties']['work_days'] as $day) {
            if ($day['check_in'] != 0) $work_days ++;
        }

        $validated['employee_properties']['birthdate'] = [
            'formatted' => Carbon::parse($validated['employee_properties']['birthdate'])->isoFormat('DD MMMM'),
            'raw' => $validated['employee_properties']['birthdate']
        ];
        $validated['employee_properties']['salary']['week'] =  floatval($validated['employee_properties']['salary']['week']);
        $validated['employee_properties']['hours_per_week'] =  intval($validated['employee_properties']['hours_per_week']);
        $hours_per_day = $validated['employee_properties']['hours_per_week'] / $work_days; 
        $validated['employee_properties']['salary']['hour'] = 
            round($validated['employee_properties']['salary']['week'] / $validated['employee_properties']['hours_per_week'], 2);
        $validated['employee_properties']['salary']['day'] = round($validated['employee_properties']['salary']['hour'] * $hours_per_day, 2);   
        $validated['password'] = bcrypt($request['employee_properties']['password']);
        
        $user = User::create($validated);
        $user->syncRoles($request->roles);

        return to_route('users.index');
    }


    public function show(User $user)
    {
        $users = UserResource::collection(User::latest()->get());
        $roles = Role::all();

        return inertia('User/Show', compact('user', 'users', 'roles'));
    }


    public function edit(User $user)
    {
        $bonuses = Bonus::where('is_active', 1)->get();
        $roles = Role::all();
        $user_roles = $user->roles->pluck('id');

        return inertia('User/Edit', compact('bonuses', 'user', 'roles', 'user_roles'));
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users,email,'.$user->id,
            'roles' => 'array|min:1',
            'employee_properties.salary.week' => 'required|numeric|min:1',
            'employee_properties.hours_per_week' => 'required|numeric|min:1',
            'employee_properties.birthdate' => 'required|date',
            'employee_properties.join_date' => 'required|date',
            'employee_properties.job_position' => 'required|string',
            'employee_properties.department' => 'required|string',
            'employee_properties.work_days' => 'array',
            'employee_properties.bonuses' => 'nullable',
        ]);

        $work_days = 0;
        foreach ($validated['employee_properties']['work_days'] as $day) {
            if ($day['check_in'] != 0) $work_days ++;
        }

        $validated['employee_properties']['birthdate'] = [
            'formatted' => Carbon::parse($validated['employee_properties']['birthdate'])->isoFormat('DD MMMM'),
            'raw' => $validated['employee_properties']['birthdate']
        ];
        $validated['employee_properties']['salary']['week'] =  floatval($validated['employee_properties']['salary']['week']);
        $validated['employee_properties']['hours_per_week'] =  intval($validated['employee_properties']['hours_per_week']);
        $hours_per_day = $validated['employee_properties']['hours_per_week'] / $work_days; 
        $validated['employee_properties']['salary']['hour'] = 
            round($validated['employee_properties']['salary']['week'] / $validated['employee_properties']['hours_per_week'], 2);
        $validated['employee_properties']['salary']['day'] = round($validated['employee_properties']['salary']['hour'] * $hours_per_day, 2);   
        $validated['employee_properties']['hours_per_day'] = round($hours_per_day, 2);

        $user->update($validated);
        $user->syncRoles($request->roles);

        return to_route('users.index');
    }


    public function destroy(User $user)
    {
        $user->delete();
    }

    // other methods
    public function getNextAttendance()
    {
        $next = auth()->user()->getNextAttendance();

        return response()->json(compact('next'));
    }

    public function setAttendance()
    {
        $next = auth()->user()->setAttendance();

        return response()->json(compact('next'));
    }

    public function resetPass(User $user)
    {
        $user->update([
            'password' => bcrypt('e3d')
        ]);
    }

    public function changeStatus(User $user)
    {
        if ($user->is_active) {
            
            $user->update([
                'is_active' => false
            ]);
        }else{

            $user->update([
                'is_active' => true
            ]);
        }

    }
}
