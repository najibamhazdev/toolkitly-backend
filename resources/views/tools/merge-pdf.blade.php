<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Merge PDF files online for free. Upload PDFs, arrange their order, and download one combined PDF with ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/merge-pdf') }}">
        <meta property="og:title" content="Merge PDF | ToolKitly">
        <meta property="og:description" content="Combine multiple PDFs into one free online PDF file.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/merge-pdf') }}">

        <title>Merge PDF | ToolKitly</title>

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
                data-tool="merge-pdf"
                data-metadata-url="{{ url('/api/tools/pdf/merge-pdf') }}"
                data-merge-url="{{ url('/api/tools/pdf/merge-pdf/merge') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
