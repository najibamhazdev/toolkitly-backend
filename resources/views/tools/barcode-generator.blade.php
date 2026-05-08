<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Create free Code 128, EAN-13, and UPC-A barcodes. Export PNG or SVG instantly with ToolKitly.">
        <link rel="canonical" href="{{ url('/barcode-generator') }}">
        <meta property="og:title" content="Barcode Generator | ToolKitly">
        <meta property="og:description" content="Generate free barcodes in your browser and export PNG or SVG.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/barcode-generator') }}">

        <title>Barcode Generator | ToolKitly</title>

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
                data-tool="barcode-generator"
                data-metadata-url="{{ url('/api/tools/barcode-generator') }}"
                data-payload-url="{{ url('/api/tools/barcode-generator/payload') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
