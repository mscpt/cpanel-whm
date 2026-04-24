<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TrackEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    private const KEYS = [
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
        $settings = Setting::whereIn('key', self::KEYS)
            ->pluck('value', 'key')
            ->toArray();

        // Fill missing keys with empty string so the view never gets undefined
        foreach (self::KEYS as $key) {
            $settings[$key] ??= '';
        }

        // Single query with conditional aggregation instead of three separate queries
        $row = TrackEvent::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN DATE(created_at) = DATE('now') THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
        ", [now()->startOfWeek()])->first();

        $trackStats = [
            'total'     => (int) $row->total,
            'today'     => (int) $row->today,
            'this_week' => (int) $row->this_week,
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
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Definições guardadas.');
    }
}
