<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Tool Analytics | ToolKitly</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm font-semibold text-[#7f5f2a]">Private dashboard</p>
                <h1 class="text-3xl font-semibold">Tool success analytics</h1>
                <p class="mt-2 text-sm text-[#655d51]">Successful tool events from the last 30 days.</p>
            </div>

            <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
                <div class="overflow-hidden rounded-lg border border-[#cdd8d2] bg-white shadow-sm">
                    <div class="border-b border-[#d9e1dc] px-4 py-3">
                        <h2 class="text-sm font-semibold">Tools</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[36rem] text-left text-sm">
                            <thead class="bg-[#f8faf8] text-xs uppercase text-[#655d51]">
                                <tr>
                                    <th class="px-4 py-3">Tool</th>
                                    <th class="px-4 py-3">Successes</th>
                                    <th class="px-4 py-3">Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($totals as $row)
                                    <tr class="border-t border-[#eef2ef]">
                                        <td class="px-4 py-3 font-semibold">{{ $row->tool }}</td>
                                        <td class="px-4 py-3">{{ number_format($row->total) }}</td>
                                        <td class="px-4 py-3">{{ number_format($row->visitors) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-[#655d51]">No success events yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid gap-4">
                    <section class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                        <h2 class="text-sm font-semibold">Daily successes</h2>
                        <div class="mt-3 grid gap-2">
                            @forelse ($daily as $row)
                                <div class="flex items-center justify-between gap-3 rounded-lg bg-[#f8faf8] px-3 py-2 text-sm">
                                    <span>{{ $row->day }}</span>
                                    <strong>{{ number_format($row->total) }}</strong>
                                </div>
                            @empty
                                <p class="text-sm text-[#655d51]">No daily data yet.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-lg border border-[#cdd8d2] bg-white p-4 shadow-sm">
                        <h2 class="text-sm font-semibold">Actions</h2>
                        <div class="mt-3 grid gap-2">
                            @forelse ($actions as $row)
                                <div class="rounded-lg bg-[#f8faf8] px-3 py-2 text-sm">
                                    <p class="font-semibold">{{ $row->tool }}</p>
                                    <p class="text-[#655d51]">{{ $row->action }} · {{ number_format($row->total) }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-[#655d51]">No action data yet.</p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>
