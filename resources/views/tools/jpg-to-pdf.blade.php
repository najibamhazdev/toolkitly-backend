<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => 'JPG to PDF',
            'description' => 'Convert JPG images to one PDF online for free with ToolKitly.',
            'path' => '/pdf/jpg-to-pdf',
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
                data-tool-kind="jpg-to-pdf"
                data-metadata-url="{{ url('/api/tools/pdf/jpg-to-pdf') }}"
                data-action-url="{{ url('/api/tools/pdf/jpg-to-pdf/process') }}"
            ></div>
            @include('partials.related-tools')
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
