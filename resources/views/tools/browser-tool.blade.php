<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">
        <link rel="canonical" href="{{ url($path) }}">
        <meta property="og:title" content="{{ $title }} | ToolKitly">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url($path) }}">

        <title>{{ $title }} | ToolKitly</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => $title,
            'description' => $description,
            'path' => $path,
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
                data-tool="browser-tool"
                data-tool-kind="{{ $toolKind }}"
                data-title="{{ $title }}"
                data-description="{{ $description }}"
                @isset($actionUrl)
                    data-action-url="{{ url($actionUrl) }}"
                @endisset
            ></div>
            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>
    </body>
</html>
