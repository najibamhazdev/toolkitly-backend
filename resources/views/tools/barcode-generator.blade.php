<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => 'Barcode Generator',
            'description' => 'Create free Code 128, EAN-13, and UPC-A barcodes. Export PNG or SVG instantly with ToolKitly.',
            'path' => '/barcode-generator',
        ])
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
            @include('partials.related-tools')
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
