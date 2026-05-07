<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Remove pages from a PDF online for free. Enter page numbers and download a cleaned PDF with ToolKitly.">
        <link rel="canonical" href="{{ url('/pdf/remove-pdf-pages') }}">
        <meta property="og:title" content="Remove PDF Pages | ToolKitly">
        <meta property="og:description" content="Delete selected pages from a PDF file for free.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/pdf/remove-pdf-pages') }}">

        <title>Remove PDF Pages | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')

            <div
                id="app"
                data-tool="pdf-utility"
                data-tool-kind="remove-pdf-pages"
                data-metadata-url="{{ url('/api/tools/pdf/remove-pdf-pages') }}"
                data-action-url="{{ url('/api/tools/pdf/remove-pdf-pages/process') }}"
            ></div>
        </div>
    </body>
</html>
