<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Rotate PDF pages online for free with ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/rotate-pdf') }}">
        <meta property="og:title" content="Rotate PDF | ToolKitly">
        <meta property="og:description" content="Rotate every page in a PDF and download a new file.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/rotate-pdf') }}">
        <title>Rotate PDF | ToolKitly</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head')
        @include('partials.adsense-head')
        @include('partials.analytics-head')
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')
            @include('partials.ad-slot', ['slot' => 'top'])
            <div id="app" data-tool="pdf-utility" data-tool-kind="rotate-pdf" data-metadata-url="{{ url('/api/tools/pdf/rotate-pdf') }}" data-action-url="{{ url('/api/tools/pdf/rotate-pdf/process') }}"></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
