<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Http\Resources\SaleResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Contact;
use App\Models\Design;
use App\Models\ExtraTimeRequest;
use App\Models\Meeting;
use App\Models\Payroll;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Quality;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Sample;
use App\Models\Storage;
use App\Models\User;

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
        $counts[] = Sample::whereNull('authorized_at')->get()->count();
        $current_user_sales_without_production = SaleResource::collection(Sale::where('user_id', auth()->id())->whereDoesntHave('productions')->get());

        // Diseños iniciados hoy por cada diseñador
        $todays_design_orders = Design::with(['designer:id,name'])
            ->whereDate('started_at', today())
            ->get()
            ->groupBy('designer.name');

        // production performance
        $collaborators_production_performance = $this->getProductionPerformance();
        $collaborators_design_performance = $this->getDesignPerformance();
        $collaborators_sales_performance = $this->getSalesPerformance();

        // birthdates
        $collaborators_birthdays = User::where('is_active', true)->whereMonth('employee_properties->birthdate->raw', '=', now()->month)
            ->get();

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

        // cumpleaños de clientes 1 semana antes
        $customers_birthdays = $this->getNextCustomersBirthday();

        // solicitud de tiempo extra
        $extra_time_request = ExtraTimeRequest::whereDate('date', '>=', today())->where('operator_id', auth()->id())->latest()->first();

        // return $collaborators_production_performance;
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
            'extra_time_request',
            'todays_design_orders',
        ));
    }

    private function getNextCustomersBirthday()
    {
        // Definir el rango de fechas de una semana antes
        $startOfWeek = today()->subWeek()->startOfWeek();
        $endOfWeek = today()->endOfWeek();

        // Crear un array de días y meses que corresponden a la semana pasada
        $daysRange = [];
        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $daysRange[] = ['day' => $date->day, 'month' => $date->month];
        }

        $customers_birthdays = Contact::with('contactable')
            ->where(function ($query) use ($daysRange) {
                foreach ($daysRange as $date) {
                    $query->orWhere(function ($query) use ($date) {
                        $query->where('birthdate_day', $date['day'])
                            ->where('birthdate_month', $date['month'] - 1);
                    });
                }
            })
            ->get();

        // Ordenar los resultados por el mes y luego por el día de nacimiento en orden descendente
        $customers_birthdays = $customers_birthdays->sort(function ($a, $b) {
            // Comparar primero el mes
            if ($a->birthdate_month == $b->birthdate_month) {
                // Si los meses son iguales, comparar los días
                return $a->birthdate_day <=> $b->birthdate_day;
            }
            // Si los meses son diferentes, comparar los meses
            return $a->birthdate_month <=> $b->birthdate_month;
        });

        // Convertir el resultado en un array sin claves asociativas
        return $customers_birthdays->values()->toArray();
    }

    private function getProductionPerformance()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Producción')
            ->with(['productions.catalogProductCompanySale.sale'])
            ->where('is_active', true)
            ->get();

        foreach ($users as $user) {
            $weekly_points = [];

            $productionsFinishedThisWeek = $user->productions()->with('catalogProductCompanySale.sale')->whereNotNull('finished_at')->whereBetween('finished_at', [$weekStart, $weekEnd])->get();
            foreach ($productionsFinishedThisWeek as $production) {
                $day = $production->finished_at->isoFormat('ddd DD MMM'); // para la clave
                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "punctuality" => 0,
                        "scrap" => 0,
                        "time" => 0,
                        "day_completed" => 0,
                        "extra_time_requests" => 0,
                        "extra_time_requests" => 0,
                        "quality_supervision" => 0,
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
                        "day_completed" => 0,
                        "extra_time_requests" => 0,
                        "quality_supervision" => 0,
                    ];
                }

                // agregar puntos de tiempo extra aceptado en caso de tener
                $accepted_extra_time_request = $user->extraTimeRequests()->whereDate('date', $payroll->pivot->date)->where('is_accepted', true)->first();
                if ($accepted_extra_time_request) {
                    $weekly_points[$day]["extra_time_requests"] += $accepted_extra_time_request->points;
                }

                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $weekly_points[$day]["punctuality"] += $payroll->pivot->late > 0 ? -10 : 10;
                // Restar 50 pts si no se terminó la jornada del día si es que ya ha registrado salida
                if ($payroll->pivot->check_out) {
                    $hours_to_work = $user->employee_properties['work_days'][$payroll->pivot->date->dayOfWeek]['hours'];
                    $hours_worked = $payroll->pivot->totalWorkedTime()['hours'];
                    $weekly_points[$day]["day_completed"] += $hours_worked < $hours_to_work ? -50 : 0;
                }
            }

            // Agregar puntos por registro de supervision de calidad creada
            $quality_supervision = Quality::where('supervisor_id', $user->id)->whereBetween('created_at', [$weekStart, $weekEnd])->get();
            if ($quality_supervision->count()) {
                $weekly_points[$day]["quality_supervision"] = $quality_supervision->count() * 10;
            }

            // Calcular el total de puntos
            $totalPoints = 0;
            foreach ($weekly_points as $day => $points) {
                // Suma los puntos de punctuality, scrap y time para cada día
                $dailyTotal = $points["punctuality"] + $points["scrap"] + $points["time"] + $points["day_completed"] + $points["extra_time_requests"] + $points["quality_supervision"];
                $totalPoints += $dailyTotal;
            }

            $user->weekly_points = $weekly_points; // Asignar weekly_points al usuario
            $user->total_points = $totalPoints; // Asignar total_points al usuario
            $user->productions = $productionsFinishedThisWeek;
        }

        $users = $users->sortByDesc('total_points')->values();
        $filtered = $users->sortByDesc('points')->values()->map(function ($user) {
            return [
                'name' => $user->name,
                'productions' => $user->productions,
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
                        "new_companies" => 0,
                        "new_products" => 0,
                    ];
                }

                // ventas
                $sold_products = $sale->catalogProductCompanySales;
                foreach ($sold_products as $product) {
                    $price = $product->catalogProductCompany?->new_price ?? 0;
                    $weekly_points[$day]["sales"] += $product->quantity * $price;
                    $totalMoney += $product->quantity * $price;
                }
            }

            // nuevos clientes
            $new_companies_added = $user->companies()->whereBetween('created_at', [$weekStart, $weekEnd])->get();
            foreach ($new_companies_added as $company) {
                $day = $company->created_at->isoFormat('ddd DD MMM'); // para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "sales" => 0,
                        "new_companies" => 0,
                        "new_products" => 0,
                    ];
                }

                $weekly_points[$day]["new_companies"] += 20;
            }

            // nuevos productos
            $new_products_added = $user->catalogProductsCompany()->whereBetween('created_at', [$weekStart, $weekEnd])->get();
            foreach ($new_products_added as $product) {
                $day = $product->created_at->isoFormat('ddd DD MMM'); // para la clave

                // Agregar el día al arreglo de weekly_points
                if (!isset($weekly_points[$day])) {
                    $weekly_points[$day] = [
                        "sales" => 0,
                        "new_companies" => 0,
                        "new_products" => 0,
                    ];
                }

                $weekly_points[$day]["new_products"] += 20;
            }

            // Calcular el total de puntos
            $totalPoints = 0;
            foreach ($weekly_points as $day => $points) {
                // Suma los puntos de punctuality, scrap y time para cada día
                $dailyTotal = round($points["sales"] / 100, 2) + $points["new_companies"] + $points["new_products"];
                $totalPoints += $dailyTotal;
            }

            // Calcular los puntos basados en el dinero ganado
            $user->total_points = $totalPoints;
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
