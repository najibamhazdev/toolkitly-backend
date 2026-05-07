<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Split PDF files online for free. Extract each PDF page into separate PDF files with ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/split-pdf') }}">
        <meta property="og:title" content="Split PDF | ToolKitly">
        <meta property="og:description" content="Split one PDF into separate page files for free.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/split-pdf') }}">

        <title>Split PDF | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')

            <div
                id="app"
                data-tool="pdf-utility"
                data-tool-kind="split-pdf"
                data-metadata-url="{{ url('/api/tools/pdf/split-pdf') }}"
                data-action-url="{{ url('/api/tools/pdf/split-pdf/process') }}"
            ></div>
        </div>
    </body>
</html>
