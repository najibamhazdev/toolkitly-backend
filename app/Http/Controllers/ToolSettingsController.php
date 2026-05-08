<?php

namespace App\Http\Controllers;

use App\Support\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $this->authorizeToken($request);

        return view('admin.settings', [
            'settings' => $this->settings(),
            'token' => $request->query('token'),
            'analyticsToken' => config('toolkitly.analytics.dashboard_token') ?: $request->query('token'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeToken($request);

        $validated = $request->validate([
            'google_analytics_measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^G-[A-Z0-9]+$/i'],
            'google_adsense_client' => ['nullable', 'string', 'max:64', 'regex:/^ca-pub-[0-9]+$/i'],
            'google_adsense_slot_top' => ['nullable', 'string', 'max:32', 'regex:/^[0-9]+$/'],
            'google_adsense_slot_middle' => ['nullable', 'string', 'max:32', 'regex:/^[0-9]+$/'],
            'google_adsense_slot_bottom' => ['nullable', 'string', 'max:32', 'regex:/^[0-9]+$/'],
            'max_upload_kb' => ['required', 'integer', 'min:1024', 'max:102400'],
            'temporary_file_ttl' => ['required', 'integer', 'min:300', 'max:86400'],
        ]);

        PlatformSettings::setMany($validated);

        return redirect()
            ->route('admin.settings', ['token' => $request->query('token')])
            ->with('status', 'Settings saved.');
    }

    private function settings(): array
    {
        return [
            'google_analytics_measurement_id' => PlatformSettings::get('google_analytics_measurement_id', config('toolkitly.analytics.google_measurement_id')),
            'google_adsense_client' => PlatformSettings::get('google_adsense_client', config('toolkitly.adsense.client')),
            'google_adsense_slot_top' => PlatformSettings::get('google_adsense_slot_top', config('toolkitly.adsense.slots.top')),
            'google_adsense_slot_middle' => PlatformSettings::get('google_adsense_slot_middle', config('toolkitly.adsense.slots.middle')),
            'google_adsense_slot_bottom' => PlatformSettings::get('google_adsense_slot_bottom', config('toolkitly.adsense.slots.bottom')),
            'max_upload_kb' => PlatformSettings::get('max_upload_kb', config('toolkitly.max_upload_kb')),
            'temporary_file_ttl' => PlatformSettings::get('temporary_file_ttl', config('toolkitly.temporary_file_ttl')),
        ];
    }

    private function authorizeToken(Request $request): void
    {
        $token = config('toolkitly.admin.settings_token') ?: config('toolkitly.analytics.dashboard_token');

        abort_if(! $token || ! hash_equals($token, (string) $request->query('token')), 404);
    }
}
