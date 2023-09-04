<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Bonus;
use App\Models\ChMessage;
use App\Models\Design;
use App\Models\Discount;
use App\Models\PayrollUser;
use App\Models\Production;
use App\Models\Sale;
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
        $discounts = Discount::where('is_active', 1)->get();
        $roles = Role::all();

        return inertia('User/Create', compact('employee_number', 'bonuses', 'discounts', 'roles'));
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
            'employee_properties.discounts' => 'nullable',
            'employee_properties.vacations' => 'nullable',
        ]);
        $work_days = 0;
        foreach ($validated['employee_properties']['work_days'] as $day) {
            if ($day['check_in'] != 0) $work_days++;
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

        event(new RecordCreated($user));

        return to_route('users.index');
    }

    public function show(User $user)
    {
        $user_id = $user->id;
        $users = UserResource::collection(User::latest()->get());
        $roles = Role::all();

        // PERSONAL
        $payroll_user = PayrollUser::where('user_id', $user_id)->get();
        $personal = [
            ["label" => "Dias laborados", "value" => $payroll_user->count()],
            ["label" => "Tiempo total laborado", "value" => $this->formatTime($payroll_user->sum(function ($payrollUser) {
                $result = $payrollUser->totalWorkedTime();
                // Verificar si el resultado es un array con clave 'hours' y sumar el valor 'hours' al total
                if (is_array($result) && isset($result['hours'])) {
                    return $result['hours'] * 60;
                }
                return 0; // En caso de que el resultado no sea válido
            }))],
            ["label" => "Total pagado sin bonos", "value" => '$' . number_format($payroll_user->sum(function ($payrollUser) {
                $result = $payrollUser->totalWorkedTime();
                // Verificar si el resultado es un array con clave 'hours' y sumar el valor 'hours' al total
                if (is_array($result) && isset($result['hours'])) {
                    return $result['hours'] * $payrollUser->additionals["salary"]["hour"];
                }
                return 0; // En caso de que el resultado no sea válido
            }))],
            ["label" => "Tiempo total de retardos", "value" => $this->formatTime($payroll_user->sum(function ($payrollUser) {
                return $payrollUser->late;
            }))],
        ];

        //PRODUCTION
        $productions = $user->productions;
        $production_performances = [
            ["label" => "Total de órdenes", "value" => $productions->count()],
            ["label" => "Órdenes terminadas", "value" => $productions->filter(fn ($production) => $production->finished_at !== null)->count()],
            ["label" => "Tiempo invertido en órdenes terminadas", "value" => $this->formatTime($productions->sum(function ($production) {
                return $production->getTotalTimeInMinutes() - $production->getPausaTimeInMinutes();
            }))],
            ["label" => "Tiempo estimado en órdenes terminadas", "value" => $this->formatTime($productions->sum(function ($production) {
                return ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
            }))],
        ];

        //DESIGN
        $designs = $user->designs;
        $design_performances = [
            ["label" => "Total de órdenes", "value" => $designs->count()],
            ["label" => "Órdenes terminadas", "value" => $designs->filter(fn ($design) => $design->finished_at !== null)->count()],
            ["label" => "Órdenes con retraso", "value" => $designs->filter(fn ($design) => $design->finished_at?->greaterThan($design->expected_end_at))->count()],
        ];
        return $design_performances;

        // SALES
        

        return inertia('User/Show', compact(
            'user',
            'users',
            'roles',
            'finished_production_orders',
            // 'asigned_production_orders',
            // 'total_hours_production',
            // 'total_minutes_production',
            // 'finished_design_orders',
            // 'asigned_design_orders',
            // 'sale_orders_created',
            // 'total_money_sold',
        ));
    }

    public function edit(User $user)
    {
        $bonuses = Bonus::where('is_active', 1)->get();
        $discounts = Discount::where('is_active', 1)->get();
        $roles = Role::all();
        $user_roles = $user->roles->pluck('id');

        return inertia('User/Edit', compact('bonuses', 'user', 'roles', 'discounts', 'user_roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users,email,' . $user->id,
            'roles' => 'array|min:1',
            'employee_properties.salary.week' => 'required|numeric|min:1',
            'employee_properties.hours_per_week' => 'required|numeric|min:1',
            'employee_properties.birthdate' => 'required|date',
            'employee_properties.join_date' => 'required|date',
            'employee_properties.job_position' => 'required|string',
            'employee_properties.department' => 'required|string',
            'employee_properties.work_days' => 'array',
            'employee_properties.bonuses' => 'nullable',
            'employee_properties.discounts' => 'nullable',
        ]);

        $work_days = 0;
        foreach ($validated['employee_properties']['work_days'] as $day) {
            if ($day['check_in'] != 0) $work_days++;
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

        event(new RecordEdited($user));

        return to_route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        event(new RecordDeleted($user));
    }

    // other methods
    public function getNextAttendance()
    {
        $next = auth()->user()->getNextAttendance();

        return response()->json(compact('next'));
    }

    public function getPauseStatus()
    {
        $status = auth()->user()->getPauseStatus();

        return response()->json(compact('status'));
    }

    public function setAttendance()
    {
        $user = auth()->user();

        $productions_in_progress = $user->productions()
            ->where('is_paused', false)
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->exists();

        if ($productions_in_progress) {
            return response()->json(['message' => 'Tienes órden(es) de producción en proceso. Primero debes pausarla(s)'], 422);
        } else {
            $next = $user->setAttendance();

            return response()->json(compact('next'));
        }
    }

    public function setPause()
    {
        $user = auth()->user();

        $is_paused = $user->setPause();

        $message = $is_paused
            ? "Se ha pausado tu tiempo laboral"
            : "Se ha reanudado tu tiempo laboral";

        return response()->json(['message' => $message, 'status' => $is_paused]);
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
        } else {

            $user->update([
                'is_active' => true
            ]);
        }
    }

    public function getUnseenMessages()
    {
        $unseen_messages = ChMessage::where('to_id', auth()->id())->where('seen', 0)->get()->count();

        return response()->json(['count' => $unseen_messages]);
    }

    public function getNotifications(Request $request)
    {
        $notifications = auth()->user()->notifications()->where('data->module', $request->module)->get();

        return response()->json(['items' => NotificationResource::collection($notifications)]);
    }

    public function readNotifications(Request $request)
    {
        $notifications = auth()->user()->notifications()->whereIn('id', $request->notifications_ids)->get();
        $notifications->markAsRead();

        return response()->json([]);
    }

    public function deleteNotifications(Request $request)
    {
        $notifications = auth()->user()->notifications()->whereIn('id', $request->notifications_ids)->delete();

        return response()->json([]);
    }

    private function formatTime($minutes)
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $formattedTime = '';

        if ($hours > 0) {
            $formattedTime .= $hours . 'h ';
        }

        if ($remainingMinutes > 0 || empty($formattedTime)) {
            $formattedTime .= $remainingMinutes . 'm';
        }

        return $formattedTime;
    }
}
