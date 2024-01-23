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
        // $users = UserResource::collection(User::latest()->get());
        //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
        $pre_users = UserResource::collection(User::latest()->get());
        $users = $pre_users->map(function ($user) {

            return [
                'id' => $user->id,
                'name' => $user->name,
                'is_active' => [
                    'string' => $user->is_active ? 'Activo' : 'Inactivo',
                    'bool' => boolval($user->is_active),
                ],
                'employee_properties' => $user->employee_properties,
            ];
        });

        // return $users;

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
            'employee_properties.birthdate' => 'required|date',
            'employee_properties.join_date' => 'required|date',
            'employee_properties.job_position' => 'required|string',
            'employee_properties.department' => 'required|string',
            'employee_properties.work_days' => 'array',
            'employee_properties.bonuses' => 'nullable',
            'employee_properties.discounts' => 'nullable',
            'employee_properties.vacations' => 'nullable',
            'employee_properties.skills' => 'nullable',
        ]);

        $password = $request['employee_properties']['password'];
        $validated = $this->processUserData($validated);
        $validated['password'] = bcrypt($password);
        $user = User::create($validated);
        $user->syncRoles($request->roles);

        event(new RecordCreated($user));

        return to_route('users.index');
    }

    public function show($user_id)
    {
        $user = UserResource::make(User::find($user_id));
        $roles = Role::all();

        $pre_users = User::latest()->get();
        $users = $pre_users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        });

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
            ["label" => "Total pagado sin bonos", "value" => '$' . number_format($payroll_user->sum(function ($payrollUser) use ($user_id) {
                $result = $payrollUser->totalWorkedTime();
                // Verificar si el resultado es un array con clave 'hours' y sumar el valor 'hours' al total
                if (is_array($result) && isset($result['hours'])) {
                    if ($user_id == 34) { //solo para santiago porque causa problemas
                        return $result['hours'] * 52.08;
                    }
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

        // SALES
        $sales = $user->sales;
        $sale_performances = [
            ["label" => "Total de órdenes", "value" => $sales->count()],
            ["label" => "Total vendido de órdenes autorizadas", "value" => '$' . number_format($sales->sum(function ($sale) {
                return $sale->getTotalSoldAmount();
            }), 2)],
            ["label" => "Total de cotizaciones creadas", "value" => $user->quotes->count()],
        ];

        // return $users;

        return inertia('User/Show', compact(
            'user',
            'users',
            'roles',
            'personal',
            'production_performances',
            'design_performances',
            'sale_performances',
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
            'employee_properties.birthdate' => 'required|date',
            'employee_properties.join_date' => 'required|date',
            'employee_properties.job_position' => 'required|string',
            'employee_properties.department' => 'required|string',
            'employee_properties.work_days' => 'array',
            'employee_properties.vacations' => 'array',
            'employee_properties.bonuses' => 'nullable',
            'employee_properties.discounts' => 'nullable',
            'employee_properties.skills' => 'nullable',
        ]);

        $validated = $this->processUserData($validated);
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

    public function getRequestedDays($user_id, $payroll_id)
    {
        $user = User::find($user_id);
        $payrolls_user = $user->payrolls()->wherePivot('payroll_id', $payroll_id)->get();
        $requested_payrolls_user = $payrolls_user->filter(function ($payroll) {
            return !$payroll->pivot->additionalTimeRequest()->doesntExist();
        })->values();

        return response()->json(['requested' => $requested_payrolls_user, 'all' => $payrolls_user]);
    }

    public function updatePausas(PayrollUser $payroll_user, Request $request)
    {
        $payroll_user->pausas = $request->pausas;
        $payroll_user->save();

        return response()->json([]);
    }

    public function updateVacations(User $user, Request $request)
    {
        $employee_properties = $user->employee_properties;

        // sub or add days
        if ($request->operation == '-')
            $employee_properties['vacations']['available_days'] -= $request->days;
        else
            $employee_properties['vacations']['available_days'] += $request->days;

        $user->update(['employee_properties' => $employee_properties]);

        return response()->json(['available_days' => $employee_properties['vacations']['available_days']]);
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

    private function processUserData($data)
    {
        $workDaysData = $data['employee_properties']['work_days'];
        $hoursPerWeek = 0;
        foreach ($workDaysData as &$workDay) {
            $hoursWorked = 0;
            if ($workDay['check_in'] != 0 && $workDay['check_in'] != null) {
                $checkIn = Carbon::parse($workDay['check_in']);
                $checkOut = Carbon::parse($workDay['check_out']);
                $diff = $checkOut->diffInMinutes($checkIn);
                $withFixedBreak = $diff - $workDay['break'];
                // Calcula las horas trabajadas y redondea a 2 decimales
                $hoursWorked = round($withFixedBreak / 60, 2);

                // Agrega las horas trabajadas al arreglo
            }
            $workDay['hours'] = $hoursWorked;
            $hoursPerWeek += $hoursWorked;
        }
        $salaryPerHour = $data['employee_properties']['salary']['week'] / $hoursPerWeek;
        foreach ($workDaysData as &$workDay) {
            $workDay['salary'] = round($workDay['hours'] * $salaryPerHour, 2);
        }
        $data['employee_properties']['hours_per_week'] = $hoursPerWeek;
        $data['employee_properties']['hours_per_week_formatted'] = intval($hoursPerWeek) . "h " . intval(($hoursPerWeek - intval($hoursPerWeek)) * 60) . "m";
        $data['employee_properties']['work_days'] = $workDaysData;
        $data['employee_properties']['birthdate'] = [
            'formatted' => Carbon::parse($data['employee_properties']['birthdate'])->isoFormat('DD MMMM'),
            'raw' => $data['employee_properties']['birthdate']
        ];
        $data['employee_properties']['salary']['week'] =  floatval($data['employee_properties']['salary']['week']);
        $data['employee_properties']['salary']['hour'] = round($salaryPerHour, 2);

        return $data;
    }

    public function getPendentTasks()
    {
        $pendent_tasks = auth()->user()->tasks()->where('status', '!=', 'Terminada')->get();

        return response()->json(['items' => $pendent_tasks]);
    }

    public function getAllUsers()
    {
        $users = User::whereNotIn('id', [1])->get();

        return response()->json(['items' => $users]);
    }

    public function getOperators()
    {
        $operators = User::where('is_active', true)->where('employee_properties->department', 'Producción')->get();

        return response()->json(['items' => $operators]);
    }
}
