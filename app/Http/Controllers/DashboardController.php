<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Models\AdditionalTimeRequest;
use App\Models\Design;
use App\Models\Meeting;
use App\Models\Purchase;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\Storage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $meetings = MeetingResource::collection(Meeting::with('user', 'users')->latest()->get());
        $counts[] = Quote::whereNull('authorized_at')->get()->count();
        $counts[] = Sale::whereNull('authorized_at')->get()->count();
        $counts[] = Purchase::whereNull('authorized_at')->get()->count();
        $counts[] = Design::whereNull('authorized_at')->get()->count();
        $counts[] = 0;
        $counts[] = 0;
        $counts[] = 0;
        $counts[] = 0;
        $counts[] = AdditionalTimeRequest::whereNull('authorized_at')->get()->count();

        // return $counts;

        return inertia('Dashboard/Index', compact('meetings', 'counts'));
    }
}
