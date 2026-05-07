<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Compress JPG, PNG, and WebP images online for free. Resize and convert images in your browser with ToolKitly.">
        <link rel="canonical" href="{{ url('/images/image-compressor') }}">
        <meta property="og:title" content="Image Compressor | ToolKitly">
        <meta property="og:description" content="Compress, resize, and convert images for free in your browser.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/images/image-compressor') }}">

        <title>Image Compressor | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')

            <div
                id="app"
                data-tool="image-compressor"
                data-metadata-url="{{ url('/api/tools/images/image-compressor') }}"
            ></div>
        </div>
    </body>
</html>
