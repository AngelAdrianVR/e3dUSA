<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
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
        $collaborators_production_performance = $this->getProductionPerformace();
        $collaborators_design_performance = $this->getDesignPerformace();
        $collaborators_sales_performance = $this->getSalesPerformace();

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

    private function getProductionPerformace()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Producción')
            ->with(['productions.catalogProductCompanySale'])
            ->where('is_active', true)
            ->get();


        foreach ($users as $user) {
            $points = 0;

            foreach ($user->productions as $production) {
                if ($production->finished_at && $production->created_at->between($weekStart, $weekEnd)) {
                    // Calcular el tiempo estimado en minutos y agregarlo a los puntos
                    $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
                    $points += $estimatedTimeInMinutes;

                    // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
                    $points += $production->scrap ? -$production->scrap : 10;
                }
            }

            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $points += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            $user->points = $points;
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('points')->values();

        return $users;
    }

    private function getDesignPerformace()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Diseño')
            ->where('is_active', true)
            ->get();


        foreach ($users as $user) {
            $points = 0;

            foreach ($user->productions as $production) {
                if ($production->finished_at && $production->created_at->between($weekStart, $weekEnd)) {
                    // Calcular el tiempo estimado en minutos y agregarlo a los puntos
                    $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
                    $points += $estimatedTimeInMinutes;

                    // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
                    $points += $production->scrap ? -$production->scrap : 10;
                }
            }

            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $points += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            $user->points = $points;
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('points')->values();

        return $users;
    }

    private function getSalesPerformace()
    {
        $current_payroll = Payroll::getCurrent();
        $weekStart = $current_payroll->start_date;
        $weekEnd = $current_payroll->start_date->addDays(6);

        $users = User::where('employee_properties->department', 'Producción')
            ->with(['productions.catalogProductCompanySale'])
            ->where('is_active', true)
            ->get();


        foreach ($users as $user) {
            $points = 0;

            foreach ($user->productions as $production) {
                if ($production->finished_at && $production->created_at->between($weekStart, $weekEnd)) {
                    // Calcular el tiempo estimado en minutos y agregarlo a los puntos
                    $estimatedTimeInMinutes = ($production->estimated_time_hours * 60) + $production->estimated_time_minutes;
                    $points += $estimatedTimeInMinutes;

                    // Restar el valor de la propiedad scrap o sumar 10 puntos si no hay merma
                    $points += $production->scrap ? -$production->scrap : 10;
                }
            }

            // Obtener los registros de PayrollUser para el usuario en la semana en curso
            $payrolls = $user->payrolls()->wherePivotBetween('date', [$weekStart, $weekEnd])->get();

            foreach ($payrolls as $payroll) {
                // Restar el valor de 'late' o sumar 10 puntos si no hay retardo
                $points += $payroll->pivot->late > 0 ? -$payroll->pivot->late : 10;
            }

            $user->points = $points;
        }

        // Ordenar los usuarios de mayor a menor según los puntos
        $users = $users->sortByDesc('points')->values();

        return $users;
    }
}
