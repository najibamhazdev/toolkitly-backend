<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Platform Settings | ToolKitly</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm font-semibold text-[#7f5f2a]">Private dashboard</p>
                <h1 class="text-3xl font-semibold">Platform settings</h1>
                <p class="mt-2 text-sm text-[#655d51]">Manage monitoring, Google AdSense placements, uploads, and cleanup settings.</p>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-lg border border-[#b7d9ca] bg-[#edf8f3] px-4 py-3 text-sm font-semibold text-[#235f4f]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-[#e4b4aa] bg-[#fff4f1] px-4 py-3 text-sm text-[#7a2f20]">
                    <p class="font-semibold">Please check the highlighted settings.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update', ['token' => $token]) }}" class="grid gap-6">
                @csrf
                @method('PUT')

                <section class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold">Monitoring</h2>
                    <div class="mt-4 grid gap-4">
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Google Analytics measurement ID</span>
                            <input name="google_analytics_measurement_id" value="{{ old('google_analytics_measurement_id', $settings['google_analytics_measurement_id']) }}" placeholder="G-XXXXXXXXXX" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>
                    </div>
                </section>

                <section class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold">Google AdSense</h2>
                    <p class="mt-1 text-sm text-[#655d51]">Ads render only when both the publisher client and a slot ID are set.</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2 sm:col-span-2">
                            <span class="text-sm font-semibold">Publisher client</span>
                            <input name="google_adsense_client" value="{{ old('google_adsense_client', $settings['google_adsense_client']) }}" placeholder="ca-pub-1234567890123456" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Top ad slot</span>
                            <input name="google_adsense_slot_top" value="{{ old('google_adsense_slot_top', $settings['google_adsense_slot_top']) }}" placeholder="1234567890" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <span class="text-xs text-[#655d51]">Below the main navigation.</span>
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Middle ad slot</span>
                            <input name="google_adsense_slot_middle" value="{{ old('google_adsense_slot_middle', $settings['google_adsense_slot_middle']) }}" placeholder="1234567890" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <span class="text-xs text-[#655d51]">Homepage content break.</span>
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Bottom ad slot</span>
                            <input name="google_adsense_slot_bottom" value="{{ old('google_adsense_slot_bottom', $settings['google_adsense_slot_bottom']) }}" placeholder="1234567890" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                            <span class="text-xs text-[#655d51]">Before the footer on tool pages and homepage.</span>
                        </label>
                    </div>
                </section>

                <section class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold">Processing</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Max upload size KB</span>
                            <input type="number" min="1024" max="102400" name="max_upload_kb" value="{{ old('max_upload_kb', $settings['max_upload_kb']) }}" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Temporary file TTL seconds</span>
                            <input type="number" min="300" max="86400" name="temporary_file_ttl" value="{{ old('temporary_file_ttl', $settings['temporary_file_ttl']) }}" class="h-11 rounded-lg border border-[#cdd8d2] px-3 outline-none focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15">
                        </label>
                    </div>
                </section>

                <div class="flex flex-wrap gap-3">
                    <button class="rounded-lg bg-[#2f7c67] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#286b59]">Save settings</button>
                    <a href="{{ route('analytics.tool-events', ['token' => $analyticsToken]) }}" class="rounded-lg border border-[#c6d4cd] px-5 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-white">Open analytics</a>
                </div>
            </form>
        </main>
    </body>
</html>
