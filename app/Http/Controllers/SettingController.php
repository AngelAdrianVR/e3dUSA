<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SettingResource::collection(Setting::all());

        return inertia('Setting/Index', compact('settings'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'options' => 'nullable',
        ]);

        // process options
        if ($validated['options']) {
            $outputArray = [];
            foreach ($validated['options'] as $item) {
                list($name, $value) = explode('-', $item);
                $outputArray[] = ["name" => $name, "value" => $value];
            }

            $validated['options'] = $outputArray;
        }

        $setting = Setting::create($validated);
        event(new RecordCreated($setting));

        return response()->json(['item' => SettingResource::make($setting)]);
    }

    public function show(Setting $setting)
    {
        //
    }

    public function edit(Setting $setting)
    {
        //
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|string|max:255',
            'options' => 'nullable',
        ]);

        // process options
        if ($validated['options']) {
            $outputArray = [];
            foreach ($validated['options'] as $item) {
                list($name, $value) = explode('-', $item);
                $outputArray[] = ["name" => $name, "value" => $value];
            }

            $validated['options'] = $outputArray;
        }

        $setting->update($validated);
        event(new RecordEdited($setting));

        return response()->json(['item' => SettingResource::make($setting)]);
    }

    public function destroy(Setting $setting)
    {
        //
    }
}
