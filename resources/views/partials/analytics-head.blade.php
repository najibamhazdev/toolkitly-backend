<meta name="toolkitly-analytics-url" content="{{ url('/api/analytics/tool-events') }}">

@if (config('toolkitly.analytics.google_measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('toolkitly.analytics.google_measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('toolkitly.analytics.google_measurement_id') }}');
    </script>
@endif
