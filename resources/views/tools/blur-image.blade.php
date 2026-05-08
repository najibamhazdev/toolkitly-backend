<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Blur images online for free in your browser with ToolKitly.">
        <link rel="canonical" href="{{ url('/images/blur-image') }}">
        <meta property="og:title" content="Blur Image | ToolKitly">
        <meta property="og:description" content="Apply blur to images locally in your browser.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/blur-image') }}">
        <title>Blur Image | ToolKitly</title>
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
            <div id="app" data-tool="image-effect" data-tool-kind="blur-image" data-title="Blur Image" data-description="Apply blur to images locally in your browser."></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
