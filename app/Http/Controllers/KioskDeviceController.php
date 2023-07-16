<?php

namespace App\Http\Controllers;

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
        KioskDevice::create([
            'token' => $token,
        ]);

       return response()->json(['token' => $token]);
    }

    public function destroy(KioskDevice $kioskDevice)
    {
        //
    }
}
