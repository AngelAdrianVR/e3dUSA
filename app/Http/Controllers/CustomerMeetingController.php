<?php

namespace App\Http\Controllers;

use App\Models\CustomerMeeting;
use Illuminate\Http\Request;

class CustomerMeetingController extends Controller
{
    public function index()
    {
        //
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'location' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'required|string',
            'company_branch_id' => 'required',
            'company_id' => 'required',
            'contact_id' => 'required|numeric|min:1',
        ]);

        $customerMeeting = CustomerMeeting::create($validated + ['user_id' => auth()->id()]);
        $customerMeeting = CustomerMeeting::with(['user', 'contact.contactable.company'])->where('id', $customerMeeting->id)->first();

        //crear evento o tarea an calendario
        
        return response()->json(['item' => $customerMeeting]);
    }

    public function show(CustomerMeeting $customerMeeting)
    {
        //
    }

    public function edit(CustomerMeeting $customerMeeting)
    {
        //
    }

    public function update(Request $request, CustomerMeeting $customerMeeting)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'location' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'required|string',
            'company_branch_id' => 'required',
            'company_id' => 'required',
            'contact_id' => 'required|numeric|min:1',
        ]);

        $customerMeeting->update($validated);

        //atualizar evento o tarea an calendario

        
        $customerMeeting = CustomerMeeting::with(['user', 'contact.contactable.company'])->where('id', $customerMeeting->id)->first();
        return response()->json(['item' => $customerMeeting]);
    }

    public function destroy(CustomerMeeting $customerMeeting)
    {
        //
    }

    public function getSoonDates()
    {
        $dates = CustomerMeeting::with(['user', 'contact.contactable.company'])->whereDate('date', '>=', today())->get();
        
        return response()->json(['items' => $dates]); 
    }
}
