<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Http\Resources\UserResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Design;
use App\Models\Meeting;
use App\Models\Purchase;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Storage;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $meetings = MeetingResource::collection(Meeting::with('user', 'users')->whereDate('date', '>', today())->whereDate('date', '<=', today()->addDays(3))->latest()->get());
        $counts[] = Quote::whereNull('authorized_at')->get()->count();
        $counts[] = Sale::whereNull('authorized_at')->get()->count();
        $counts[] = Purchase::whereNull('authorized_at')->get()->count();
        $counts[] = Design::whereNull('authorized_at')->get()->count();
        $counts[] = Storage::lowStock()->count();
        $counts[] = 0;
        $counts[] = Design::whereNull('started_at')->get()->count();
        $counts[] = 0;
        $counts[] = AdditionalTimeRequest::whereNull('authorized_at')->get()->count();

        // week performance collaborators
        $collaborators_performance = [];

        // birthdates
        $collaborators_birthdays = User::whereDate('employee_properties->birthdate', today())->get();

        // recently added
        $collaborators_added = User::whereDate('employee_properties->join_date', '<=', today())->whereDate('employee_properties->join_date', '>', today()->subDays(3))->get();

        // anniversaries
        $collaborators_anniversaires = User::whereRaw("DAY(employee_properties->'$.join_date') = ?", [today()->day])
            ->whereRaw("MONTH(employee_properties->'$.join_date') = ?", [today()->month])
            ->get();

        // customers birthdays
        $customers_birthdays = [];

        return $collaborators_anniversaires;

        return inertia('Dashboard/Index', compact('meetings', 'counts'));
    }
}
