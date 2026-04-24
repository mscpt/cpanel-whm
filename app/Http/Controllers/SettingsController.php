<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TrackEvent;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private array $keys = [
        'meta_pixel_id',
        'ga_measurement_id',
        'gtm_container_id',
        'custom_head_scripts',
        'custom_body_scripts',
        'company_name',
        'company_logo_url',
        'company_address',
        'company_email',
        'company_phone',
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->keys as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        $trackStats = [
            'total'    => TrackEvent::count(),
            'today'    => TrackEvent::whereDate('created_at', today())->count(),
            'this_week'=> TrackEvent::where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        return view('settings.index', compact('settings', 'trackStats'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'meta_pixel_id'       => 'nullable|string|max:50',
            'ga_measurement_id'   => 'nullable|string|max:50',
            'gtm_container_id'    => 'nullable|string|max:50',
            'custom_head_scripts' => 'nullable|string',
            'custom_body_scripts' => 'nullable|string',
            'company_name'        => 'nullable|string|max:255',
            'company_logo_url'    => 'nullable|url|max:500',
            'company_address'     => 'nullable|string|max:500',
            'company_email'       => 'nullable|email|max:255',
            'company_phone'       => 'nullable|string|max:50',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Definições guardadas.');
    }
}
