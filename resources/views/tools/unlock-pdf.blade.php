<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Unlock password-protected PDF files online using ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/unlock-pdf') }}">
        <meta property="og:title" content="Unlock PDF | ToolKitly">
        <meta property="og:description" content="Remove a known PDF password and download an unlocked copy.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/unlock-pdf') }}">
        <title>Unlock PDF | ToolKitly</title>
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
            <div id="app" data-tool="pdf-utility" data-tool-kind="unlock-pdf" data-metadata-url="{{ url('/api/tools/pdf/unlock-pdf') }}" data-action-url="{{ url('/api/tools/pdf/unlock-pdf/process') }}"></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
