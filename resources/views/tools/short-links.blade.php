<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Create free short links with click tracking and optional expiration using ToolKitly.">
        <link rel="canonical" href="{{ url('/short-links') }}">
        <meta property="og:title" content="Short Links | ToolKitly">
        <meta property="og:description" content="Create beautiful short links with simple analytics.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/short-links') }}">

        <title>Short Links | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')

            <div
                id="app"
                data-tool="short-links"
                data-metadata-url="{{ url('/api/tools/short-links') }}"
                data-create-url="{{ url('/api/tools/short-links') }}"
            ></div>
        </div>
    </body>
</html>
