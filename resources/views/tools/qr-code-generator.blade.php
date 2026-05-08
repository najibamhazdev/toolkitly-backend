<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Create free QR codes for URLs, Wi-Fi networks, business cards, and text. Export PNG or SVG instantly with ToolKitly.">
        <link rel="canonical" href="{{ url('/qr-code-generator') }}">
        <meta property="og:title" content="QR Code Generator | ToolKitly">
        <meta property="og:description" content="Generate free QR codes in your browser and export PNG or SVG.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/qr-code-generator') }}">

        <title>QR Code Generator | ToolKitly</title>

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
                data-tool="qr-code-generator"
                data-metadata-url="{{ url('/api/tools/qr-code-generator') }}"
                data-payload-url="{{ url('/api/tools/qr-code-generator/payload') }}"
                data-initial-url="{{ url('/qr-code-generator') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
