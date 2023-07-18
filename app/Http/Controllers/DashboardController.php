<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Http\Resources\UserResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Contact;
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
            'collaborators_performance',
            'collaborators_birthdays',
            'collaborators_added',
            'collaborators_anniversaires',
            'customers_birthdays'
        ));
    }
}
