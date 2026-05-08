<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => 'Remove PDF Pages',
            'description' => 'Remove pages from a PDF online for free. Enter page numbers and download a cleaned PDF with ToolKitly.',
            'path' => '/pdf/remove-pdf-pages',
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
                data-tool-kind="remove-pdf-pages"
                data-metadata-url="{{ url('/api/tools/pdf/remove-pdf-pages') }}"
                data-action-url="{{ url('/api/tools/pdf/remove-pdf-pages/process') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
