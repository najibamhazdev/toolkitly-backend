@php
    $adsenseClient = \App\Support\PlatformSettings::get('google_adsense_client', config('toolkitly.adsense.client'));
    $slotId = \App\Support\PlatformSettings::get("google_adsense_slot_{$slot}", config("toolkitly.adsense.slots.{$slot}"));
@endphp

@if ($adsenseClient && $slotId)
    <aside class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8" aria-label="Advertisement">
        <div class="rounded-lg border border-[#d9e1dc] bg-white p-3 text-center">
            <p class="mb-2 text-xs font-semibold uppercase text-[#7f5f2a]">Advertisement</p>
            <ins
                class="adsbygoogle"
                style="display:block"
                data-ad-client="{{ $adsenseClient }}"
                data-ad-slot="{{ $slotId }}"
                data-ad-format="auto"
                data-full-width-responsive="true"
            ></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </aside>
@endif
