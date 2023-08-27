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
        //PRODUCTION
        $asigned_production_orders = Production::where('operator_id', $user_id)->get();
        $finished_production_orders = Production::where('operator_id', $user_id)->whereNotNull('finished_at')->get();
        $total_hours_production = $finished_production_orders->sum('estimated_time_hours');
        $total_minutes_production = $finished_production_orders->sum('estimated_time_minutes');
        //Designer
        $asigned_design_orders = Design::where('designer_id', $user_id)->get();
        $finished_design_orders = Design::where('designer_id', $user_id)->whereNotNull('finished_at')->get();
        // $total_hours_design = null;
        // $total_minutes_design = null;

        // if (isset($finished_design_orders)) {

        //     $totalSeconds = 0;
        //     foreach ($finished_design_orders as $item) {
        //         $startedAt = Carbon::parse($item->started_at);
        //         $finishedAt = Carbon::parse($item->finished_at);

        //         if ($startedAt && $finishedAt) {
        //             $differenceInSeconds = $finishedAt->diffInSeconds($startedAt);
        //             $totalSeconds += $differenceInSeconds;
        //         }
        //     }
        //     // Convertir los segundos totales en horas, minutos y segundos
        //     $total_hours_design = intdiv($totalSeconds, 3600);
        //     $total_minutes_design = $totalSeconds % 3600;
        // }

        //Seller
        $sale_orders_created = Sale::with('catalogProductCompanySales.catalogProductCompany')->where('user_id', $user_id)->get();
        $total_money_sold = 0;
        
        $sale_orders_created->each(function ($sale) use (&$totalMoneySold) {
            if (isset($sale['catalog_product_company_sales']) && is_array($sale['catalog_product_company_sales'])) {
                foreach ($sale['catalog_product_company_sales'] as $productSale) {
                    $quantity = $productSale['quantity'] ?? 0;
                    $newPrice = $productSale['catalog_product_company']['new_price'] ?? 0;
                    $totalMoneySold += $quantity * $newPrice;
                }
            }
        });
            

        // return $total_money_sold;

        return inertia('User/Show', compact(
            'user',
            'users',
            'roles',
            'finished_production_orders',
            'asigned_production_orders',
            'total_hours_production',
            'total_minutes_production',
            'finished_design_orders',
            'asigned_design_orders',
            // 'total_hours_design',
            // 'total_minutes_design',
            'sale_orders_created',
            'total_money_sold',
        ));
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
}
