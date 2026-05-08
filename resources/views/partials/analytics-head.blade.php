<meta name="toolkitly-analytics-url" content="{{ url('/api/analytics/tool-events') }}">

@php
    $googleMeasurementId = \App\Support\PlatformSettings::get('google_analytics_measurement_id', config('toolkitly.analytics.google_measurement_id'));
@endphp

@if ($googleMeasurementId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $googleMeasurementId }}');
    </script>
@endif
