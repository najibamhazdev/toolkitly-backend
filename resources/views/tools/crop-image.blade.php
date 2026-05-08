<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Crop images online for free in your browser. Upload an image, set crop dimensions, and download the result.">
        <link rel="canonical" href="{{ url('/images/crop-image') }}">
        <meta property="og:title" content="Crop Image | ToolKitly">
        <meta property="og:description" content="Crop images locally in your browser and download PNG or JPG.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/crop-image') }}">

        <title>Crop Image | ToolKitly</title>

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

            <div id="app" data-tool="crop-image"></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
