<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">
        <link rel="canonical" href="{{ url($path) }}">
        <title>{{ $title }} | ToolKitly</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', compact('title', 'description', 'path'))
        @include('partials.adsense-head')
        @include('partials.analytics-head')
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')
            <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-semibold">{{ $title }}</h1>
                <div class="mt-5 rounded-lg border border-[#cdd8d2] bg-white p-5 text-sm leading-7 text-[#5f574c] shadow-sm">
                    {!! $content !!}
                </div>
            </main>
            @include('partials.footer')
        </div>
    </body>
</html>
