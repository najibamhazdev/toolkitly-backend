<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Convert JPG, PNG, and WebP images online for free. Change image formats in your browser with ToolKitly.">
        <link rel="canonical" href="{{ url('/images/image-converter') }}">
        <meta property="og:title" content="Image Converter | ToolKitly">
        <meta property="og:description" content="Convert JPG to PNG, PNG to WebP, and WebP to JPG for free.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/image-converter') }}">

        <title>Image Converter | ToolKitly</title>

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
                data-tool="image-converter"
                data-metadata-url="{{ url('/api/tools/images/image-converter') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
