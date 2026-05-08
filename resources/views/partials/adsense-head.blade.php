@php
    $adsenseClient = \App\Support\PlatformSettings::get('google_adsense_client', config('toolkitly.adsense.client'));
@endphp

@if ($adsenseClient)
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClient }}" crossorigin="anonymous"></script>
@endif
