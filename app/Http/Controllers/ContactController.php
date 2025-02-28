<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
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
        //
    }

    public function show(Contact $contact)
    {
        //
    }

    public function edit(Contact $contact)
    {
        //
    }

    public function update(Request $request, Contact $contact)
    {
        $contact->update(array_filter([
            'phone' => $request->phone,
            'email' => $request->email,
            'charge' => $request->charge,
            'birthdate_day' => $request->birthdate_day,
            'birthdate_month' => $request->birthdate_month
        ]));

    }

    public function destroy(Contact $contact)
    {
        //
    }
}
