<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Convert JPG images to one PDF online for free with ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/jpg-to-pdf') }}">
        <meta property="og:title" content="JPG to PDF | ToolKitly">
        <meta property="og:description" content="Combine JPG images into one PDF file for free.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/jpg-to-pdf') }}">

        <title>JPG to PDF | ToolKitly</title>

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
                data-tool="pdf-utility"
                data-tool-kind="jpg-to-pdf"
                data-metadata-url="{{ url('/api/tools/pdf/jpg-to-pdf') }}"
                data-action-url="{{ url('/api/tools/pdf/jpg-to-pdf/process') }}"
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
