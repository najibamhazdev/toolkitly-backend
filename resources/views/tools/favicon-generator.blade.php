<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Generate favicon.ico, app icons, and browser icon PNG sizes online for free with ToolKitly.">
        <link rel="canonical" href="{{ url('/images/favicon-generator') }}">
        <meta property="og:title" content="Favicon Generator | ToolKitly">
        <meta property="og:description" content="Create favicon.ico and app icon PNG files from one image.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/favicon-generator') }}">

        <title>Favicon Generator | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')

            <div
                id="app"
                data-tool="favicon-generator"
                data-metadata-url="{{ url('/api/tools/images/favicon-generator') }}"
            ></div>
        </div>
    </body>
</html>
