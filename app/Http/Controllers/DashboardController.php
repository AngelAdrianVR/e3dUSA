<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Http\Resources\PayrollUserResource;
use App\Http\Resources\SaleResource;
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
        $current_user_sales_without_production = SaleResource::collection(Sale::where('user_id', auth()->id())->whereDoesntHave('productions')->get());

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
            'customers_birthdays',
            'current_user_sales_without_production',
        ));
    }

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
            $weekly_points = [];

            $productionsFinishedThisWeek = $user->productions()->whereNotNull('finished_at')->whereBetween('finished_at', [$weekStart, $weekEnd])->get();
            foreach ($productionsFinishedThisWeek as $production) {
                $day = $production->finished_at->isoFormat('ddd DD MMM'); // para la clave
                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "punctuality" => 0,
                        "scrap" => 0,
                        "time" => 0,
                    ];
                }

                // Calcular el tiempo estimado en minutos y agregarlo a los puntos
                $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
                $weekly_points[$day]["time"] += $estimatedTimeInMinutes;

                // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
                $weekly_points[$day]["scrap"] += $production->scrap ? -$production->scrap : 1;
            }
            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                $day = $payroll->pivot->date->isoFormat('ddd DD MMM'); // Formato "D d M" para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "punctuality" => 0,
                        "scrap" => 0,
                        "time" => 0,
                    ];
                }

                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $weekly_points[$day]["punctuality"] += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            // Calcular el total de puntos
            $totalPoints = 0;
            foreach ($weekly_points as $day => $points) {
                // Suma los puntos de punctuality, scrap y time para cada día
                $dailyTotal = $points["punctuality"] + $points["scrap"] + $points["time"];
                $totalPoints += $dailyTotal;
            }
            
            $user->weekly_points = $weekly_points; // Asignar weekly_points al usuario
            $user->total_points = $totalPoints; // Asignar total_points al usuario
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
            $weekly_points = []; // Inicializar weekly_points como un arreglo

            $totalTimeDifference = 0;
            $designs = $user->designs()->whereNotNull('started_at')->whereBetween('finished_at', [$weekStart, $weekEnd])->get();

            foreach ($designs as $design) {
                $day = $design->finished_at->isoFormat('ddd DD MMM'); // para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "punctuality" => 0,
                        "on_time" => 0,
                        "average" => 0,
                    ];
                }
                // Verificar si la orden se finalizó a tiempo
                if ($design->finished_at->greaterThanOrEqualTo($design->expected_end_at)) {
                    $weekly_points[$day]["on_time"] += 10;
                }

                // Calcular la diferencia en minutos entre started_at y expected_end_at
                $timeDifferenceMinutes = $design->started_at->diffInMinutes($design->expected_end_at);
                $totalTimeDifference += $timeDifferenceMinutes;

                // Obtener los registros de PayrollUser para el usuario en la semana en curso
                $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

                foreach ($payrolls as $payroll) {
                    $day = $design->finished_at->isoFormat('ddd DD MMM'); // para la clave

                    // Agregar el día al arreglo de weekly_points
                    if (!isset($weekly_points[$day])) {
                        $weekly_points[$day] = [
                            "punctuality" => 0,
                            "on_time" => 0,
                            "average" => 0,
                        ];
                    }

                    // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                    $weekly_points[$day]["punctuality"] += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
                }
            }

            // Calcular el promedio de tiempo en minutos ajustado a las horas por día del usuario
            if (isset($user->employee_properties['hours_per_day'])) {
                $hoursPerDay = $user->employee_properties['hours_per_day'];
            } else {
                $hoursPerDay = 8;
            }
            $totalTimeDifference = $totalTimeDifference % (1440 * $hoursPerDay); // Tomar solo las horas trabajadas
            $averageTimeDifference = $totalTimeDifference > 0 ? $totalTimeDifference / count($designs) : 0;

            // Calcular los puntos basados en el promedio de tiempo
            // $userPoints["average"] = ($averageTimeDifference > 0) ? 10000 / ($averageTimeDifference * count($weekly_points)) : 0;
            $daily_average = ($averageTimeDifference > 0) ? 10000 / ($averageTimeDifference * count($weekly_points)) : 0;
            foreach ($weekly_points as $day => $value) {
                $weekly_points[$day]["average"] = round($daily_average);
            }

            // Calcular el total de puntos
            $totalPoints = 0;
            foreach ($weekly_points as $day => $points) {
                // Suma los puntos de punctuality, scrap y time para cada día
                $dailyTotal = $points["punctuality"] + $points["on_time"] + $points["average"];
                $totalPoints += $dailyTotal;
            }

            $user->weekly_points = $weekly_points; // Asignar weekly_points al usuario
            $user->total_points = $totalPoints; // Asignar total_points al usuario
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('total_points')->values();
        $filtered = $users->sortByDesc('total_points')->values()->map(function ($user) {
            return [
                'name' => $user->name,
                'total_points' => $user->total_points,
                'weekly_points' => $user->weekly_points,
            ];
        });
        return $filtered;
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
            $weekly_points = []; // Inicializar weekly_points como un arreglo

            $totalMoney = 0;
            $sales = $user->sales()->whereNotNull('authorized_at')->whereBetween('authorized_at', [$weekStart, $weekEnd])->get();

            foreach ($sales as $sale) {
                $day = $sale->authorized_at->isoFormat('ddd DD MMM'); // para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "sales" => 0,
                    ];
                }

                $sold_products = $sale->catalogProductCompanySales;

                foreach ($sold_products as $product) {
                    $weekly_points[$day]["sales"] += $product->quantity * $product->catalogProductCompany->new_price;
                    $totalMoney += $product->quantity * $product->catalogProductCompany->new_price;
                }
            }

            // Calcular los puntos basados en el dinero ganado
            $user->total_points = round($totalMoney / 100, 2);
            $user->weekly_points = $weekly_points; // Asignar weekly_points al usuario
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('total_points')->values();

        $filtered = $users->sortByDesc('total_points')->values()->map(function ($user) {
            return [
                'name' => $user->name,
                'total_points' => $user->total_points,
                'weekly_points' => $user->weekly_points,
            ];
        });
        return $filtered;
    }

    // CRM
    public function crmDashboard()
    {
        return inertia('CRM/Dashboard');
    }
}
