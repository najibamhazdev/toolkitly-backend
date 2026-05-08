<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => 'PDF to JPG',
            'description' => 'Convert PDF pages to JPG images online for free with ToolKitly.',
            'path' => '/pdf/pdf-to-jpg',
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
                data-tool="pdf-utility"
                data-tool-kind="pdf-to-jpg"
                data-metadata-url="{{ url('/api/tools/pdf/pdf-to-jpg') }}"
                data-action-url="{{ url('/api/tools/pdf/pdf-to-jpg/process') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
