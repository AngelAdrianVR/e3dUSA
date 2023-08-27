<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Models\KioskDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KioskDeviceController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $token = Str::random(40);
        $kiosko = KioskDevice::create([
            'token' => $token,
        ]);

        event(new RecordCreated($kiosko));

       return response()->json(['token' => $token]);
    }

    public function destroy(KioskDevice $kioskDevice)
    {
        //
    }
}
