<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Http\Resources\PayrollUserResource;
use App\Http\Resources\UserResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Contact;
use App\Models\Design;
use App\Models\Meeting;
use App\Models\Payroll;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Storage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $meetings = MeetingResource::collection(Meeting::with('user', 'users')
            ->whereDate('date', '>', today())
            ->whereDate('date', '<=', today()->addDays(3))
            ->where('user_id', auth()->id())
            ->orWhere(function ($query) {
                $query->whereHas('users', function ($query) {
                    $query->where('meeting_user.user_id', auth()->id());
                });
            })->latest()->get());
        $counts[] = Quote::whereNull('authorized_at')->get()->count();
        $counts[] = Sale::whereNull('authorized_at')->get()->count();
        $counts[] = Purchase::whereNull('authorized_at')->get()->count();
        $counts[] = Design::whereNull('authorized_at')->get()->count();
        $counts[] = Storage::lowStock()->count();
        $counts[] = Production::whereNull('started_at')->get()->count();
        $counts[] = Design::whereNull('started_at')->get()->count();
        $counts[] = Sale::whereDoesntHave('productions')->get()->count();
        $counts[] = AdditionalTimeRequest::whereNull('authorized_at')->get()->count();

        // production performance
        $collaborators_production_performance = $this->getProductionPerformance();
        $collaborators_design_performance = $this->getDesignPerformance();
        $collaborators_sales_performance = $this->getSalesPerformance();
        // return $collaborators_production_performance;

        // birthdates
        $collaborators_birthdays = User::whereDate('employee_properties->birthdate', today())->get();

        // recently added
        $collaborators_added = User::whereDate('employee_properties->join_date', '<=', today())
            ->whereDate('employee_properties->join_date', '>', today()->subDays(3))
            ->get();

        // anniversaries
        $collaborators_anniversaires = User::whereDay('employee_properties->join_date', today()->day)
            ->whereMonth('employee_properties->join_date', today()->month)
            ->get()
            ->map(function ($user) {
                $years = today()->diffInYears($user->employee_properties['join_date']);
                $user->years = $years;

                return $user;
            });

        // customers birthdays
        $customers_birthdays = Contact::with('contactable')
            ->where('birthdate_day', today()->day)
            ->where('birthdate_month', today()->month)
            ->get();

        return inertia('Dashboard/Index', compact(
            'meetings',
            'counts',
            'collaborators_production_performance',
            'collaborators_design_performance',
            'collaborators_sales_performance',
            'collaborators_birthdays',
            'collaborators_added',
            'collaborators_anniversaires',
            'customers_birthdays'
        ));
    }

    // private function getProductionPerformance()
    // {
    //     $current_payroll = Payroll::getCurrent();
    //     $weekStart = $current_payroll->start_date;
    //     $weekEnd = $current_payroll->start_date->addDays(6);

    //     $users = User::where('employee_properties->department', 'Producción')
    //         ->with(['productions.catalogProductCompanySale'])
    //         ->where('is_active', true)
    //         ->get();


    //     foreach ($users as $user) {
    //         $points = 0;

    //         foreach ($user->productions as $production) {
    //             if ($production->finished_at && $production->created_at->between($weekStart, $weekEnd)) {
    //                 // Calcular el tiempo estimado en minutos y agregarlo a los puntos
    //                 $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
    //                 $points += $estimatedTimeInMinutes;

    //                 // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
    //                 $points += $production->scrap ? -$production->scrap : 10;
    //             }
    //         }

    //         // Obtener los registros de PayrollUser para el usuario en la semana en curso
    //         $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

    //         foreach ($payrolls as $payroll) {
    //             // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
    //             $points += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
    //         }

    //         $user->points = $points;
    //         $user->payrolls = $payrolls;
    //     }

    //     // Ordenar los usuarios de mayor a menor según los puntos
    //     $users = $users->sortByDesc('points')->values();
    //     $filteredUsers = $users->sortByDesc('points')->values()->map(function ($user) {
    //         return [
    //             'name' => $user->name,
    //             'points' => $user->points,
    //             'payrolls' => $user->payrolls,
    //         ];
    //     });

    //     return $filteredUsers;
    // }

    private function getProductionPerformance()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Producción')
            ->with(['productions.catalogProductCompanySale'])
            ->where('is_active', true)
            ->get();

        foreach ($users as $user) {
            $userPoints = [
                "punctuality" => 0,
                "scrap" => 0,
                "time" => 0,
                "weekly_points" => [] // Inicializar weekly_points como un arreglo
            ];

            foreach ($user->productions as $production) {
                if ($production->finished_at && $production->created_at->between($weekStart, $weekEnd)) {
                    $day = $production->created_at->format('D d M'); // Formato "D d M" para la clave

                    // Agregar el día al arreglo de weekly_points
                    if (!isset($userPoints["weekly_points"][$day])) {
                        $userPoints["weekly_points"][$day] = [
                            "punctuality" => 0,
                            "scrap" => 0,
                            "time" => 0,
                        ];
                    }

                    // Calcular el tiempo estimado en minutos y agregarlo a los puntos
                    $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
                    $userPoints["time"] += $estimatedTimeInMinutes;
                    $userPoints["weekly_points"][$day]["time"] += $estimatedTimeInMinutes;

                    // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
                    $userPoints["scrap"] += $production->scrap ? -$production->scrap : 10;
                    $userPoints["weekly_points"][$day]["scrap"] += $production->scrap ? -$production->scrap : 10;
                }
            }

            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                $day = $payroll->pivot->date->format('D d M'); // Formato "D d M" para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($userPoints["weekly_points"][$day])) {
                    $userPoints["weekly_points"][$day] = [
                        "punctuality" => 0,
                        "scrap" => 0,
                        "time" => 0,
                    ];
                }

                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $userPoints["punctuality"] += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
                $userPoints["weekly_points"][$day]["punctuality"] += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            // Calcular el total de puntos
            $totalPointsArray = [];
            foreach ($userPoints["weekly_points"] as $dayPoints) {
                $totalPointsArray[] = $dayPoints["punctuality"] + $dayPoints["scrap"] + $dayPoints["time"];
            }

            $userPoints["total_points"] = array_sum($totalPointsArray);

            $user->weekly_points = $userPoints["weekly_points"]; // Asignar weekly_points al usuario
            $user->total_points = $userPoints["total_points"]; // Asignar total_points al usuario
        }

        $users = $users->sortByDesc('total_points')->values();
        $filtered = $users->sortByDesc('points')->values()->map(function ($user) {
            return [
                'name' => $user->name,
                'total_points' => $user->total_points,
                'weekly_points' => $user->weekly_points,
            ];
        });
        return $filtered;
    }


    private function getDesignPerformance()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Diseño')
            ->where('is_active', true)
            ->get();

        foreach ($users as $user) {
            $points = 0;
            $totalTimeDifference = 0;
            $designs = $user->designs()->whereNotNull('started_at')->whereBetween('finished_at', [$weekStart, $weekEnd])->get();

            foreach ($designs as $design) {
                // Verificar si la orden se finalizó a tiempo
                if ($design->finished_at->greaterThanOrEqualTo($design->expected_end_at)) {
                    $points += 10;
                }

                // Calcular la diferencia en minutos entre started_at y expected_end_at
                $timeDifferenceMinutes = $design->started_at->diffInMinutes($design->expected_end_at);
                $totalTimeDifference += $timeDifferenceMinutes;
            }

            // Calcular el promedio de tiempo en minutos ajustado a las horas por día del usuario
            $hoursPerDay = $user->employee_properties['hours_per_day'];
            $totalTimeDifference = $totalTimeDifference % (1440 * $hoursPerDay); // Tomar solo las horas trabajadas
            $averageTimeDifference = $totalTimeDifference > 0 ? $totalTimeDifference / count($designs) : 0;

            // Calcular los puntos basados en el promedio de tiempo
            $points += ($averageTimeDifference > 0) ? 10000 / $averageTimeDifference : 0;

            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $points += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            $user->points = round($points);
        }


        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('points')->values();

        return $users;
    }

    private function getSalesPerformance()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Ventas')
            ->where('is_active', true)
            ->get();

        foreach ($users as $user) {
            $points = 0;
            $totalMoney = 0;
            $sales = $user->sales()->whereNotNull('authorized_at')->whereBetween('authorized_at', [$weekStart, $weekEnd])->get();

            foreach ($sales as $sale) {
                $sold_products = $sale->catalogProductCompanySales;

                foreach ($sold_products as $product) {
                    $totalMoney = $product->quantity * $product->catalogProductCompany->new_price;
                }
            }

            // Calcular los puntos basados en el dinero ganado
            $points = round($totalMoney / 100);

            $user->points = $points;
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('points')->values();

        return $users;
    }
}
