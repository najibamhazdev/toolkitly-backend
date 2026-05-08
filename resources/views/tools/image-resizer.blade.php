<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Resize images online for Instagram, Facebook, LinkedIn, and custom sizes. Free browser-based image resizer by ToolKitly.">
        <link rel="canonical" href="{{ url('/images/image-resizer') }}">
        <meta property="og:title" content="Image Resizer | ToolKitly">
        <meta property="og:description" content="Resize images for social media presets or custom dimensions.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/image-resizer') }}">

        <title>Image Resizer | ToolKitly</title>

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

            <div
                id="app"
                data-tool="image-resizer"
                data-metadata-url="{{ url('/api/tools/images/image-resizer') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
