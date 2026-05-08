<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.seo-head', [
            'title' => 'Free Online Tools for PDFs, Images, Developers & SEO',
            'description' => 'Use 35+ free online tools to generate QR codes, merge PDFs, compress images, format JSON, build UTM links, create SEO tags, and simplify everyday digital tasks.',
            'path' => '/',
            'schemaType' => 'WebSite',
        ])
        @include('partials.adsense-head')
        @include('partials.analytics-head')
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-[#171411] antialiased">
        <div class="min-h-screen">
            @include('partials.navigation')
            @include('partials.ad-slot', ['slot' => 'top'])

            <main>
                <section class="border-b border-[#d9e1dc] bg-white">
                    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-8 lg:py-16">
                        <div>
                            <img src="{{ asset('images/toolkitly-logo.png') }}" alt="ToolKitly" class="mb-5 h-16 w-72 object-contain object-left lg:h-20 lg:w-96">
                            <p class="text-sm font-semibold text-[#7f5f2a]">Your Digital Toolbox</p>
                            <h1 class="mt-4 max-w-4xl text-4xl font-semibold tracking-normal text-[#101820] sm:text-5xl">Free Online Tools for Everyday Tasks</h1>
                            <p class="mt-5 max-w-3xl text-lg leading-8 text-[#5f574c]">Generate, convert, optimize, and simplify with 35+ free tools for PDFs, images, developers, SEO, and more.</p>
                            <div class="mt-7 flex flex-wrap gap-3">
                                <a href="#tools" class="rounded-lg bg-[#2f7c67] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#286b59]">Explore Tools</a>
                                <a href="#popular" class="rounded-lg border border-[#c6d4cd] px-5 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#6d8d80] hover:bg-[#f4f7f5]">Popular Tools</a>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-5 shadow-sm">
                            <p class="text-sm font-semibold text-[#171411]">Fast, free, no signup</p>
                            <div class="mt-4 grid gap-3 text-sm text-[#5f574c]">
                                <p>PDFs, images, developer utilities, SEO tools, URLs, and calculators.</p>
                                <p>Most tools run in your browser. Uploaded files are temporary and auto-cleaned.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8" data-tool-search>
                    <label for="tool-search" class="text-sm font-semibold text-[#2d2923]">Search tools</label>
                    <div class="relative mt-2">
                        <input id="tool-search" type="search" placeholder="Search tools..." autocomplete="off" class="h-14 w-full rounded-lg border border-[#cdd8d2] bg-white px-4 text-base shadow-sm outline-none transition focus:border-[#2f7c67] focus:ring-4 focus:ring-[#2f7c67]/15" data-tool-search-input>
                        <div class="absolute left-0 right-0 top-full z-20 mt-2 hidden overflow-hidden rounded-lg border border-[#cdd8d2] bg-white shadow-lg" data-tool-search-results></div>
                    </div>
                </section>

                <section id="popular" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="mb-5">
                        <p class="text-sm font-semibold text-[#7f5f2a]">Most Used Tools</p>
                        <h2 class="text-2xl font-semibold">Start with popular utilities</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach (array_slice($tools, 0, 6) as $tool)
                            <a href="{{ url($tool['url']) }}" class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm transition hover:border-[#2f7c67] hover:shadow-md">
                                <p class="text-xs font-semibold uppercase text-[#7f5f2a]">{{ $tool['category'] }}</p>
                                <h3 class="mt-2 text-lg font-semibold">{{ $tool['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-[#5f574c]">{{ $tool['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>

                @include('partials.ad-slot', ['slot' => 'middle'])

                <section class="bg-white py-10">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="mb-5">
                            <p class="text-sm font-semibold text-[#7f5f2a]">Categories</p>
                            <h2 class="text-2xl font-semibold">Find tools by workflow</h2>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ([
                                ['PDF Tools', '/pdf/merge-pdf', 'Merge, split, convert, compress, and protect PDFs.'],
                                ['Image Tools', '/images/image-compressor', 'Compress, resize, convert, crop, blur, and watermark images.'],
                                ['Developer Tools', '/json-formatter', 'Format data, generate IDs, decode tokens, and hash text.'],
                                ['Web & SEO Tools', '/meta-tag-generator', 'Generate tags, schema, sitemaps, and check redirects.'],
                                ['Design Tools', '/color-picker', 'Pick colors, gradients, and CSS shadows.'],
                                ['URL Tools', '/utm-builder', 'Shorten, encode, parse, and build campaign URLs.'],
                                ['Calculators', '/percentage-calculator', 'Calculate age, dates, percentages, and units.'],
                                ['Code Tools', '/qr-code-generator', 'Create QR codes and barcodes.'],
                            ] as $category)
                                <a href="{{ url($category[1]) }}" class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4 transition hover:border-[#2f7c67]">
                                    <h3 class="font-semibold">{{ $category[0] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-[#5f574c]">{{ $category[2] }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="tools" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="mb-5">
                        <p class="text-sm font-semibold text-[#7f5f2a]">Featured Tools</p>
                        <h2 class="text-2xl font-semibold">Useful tools, one click away</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($tools as $tool)
                            <a href="{{ url($tool['url']) }}" class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm transition hover:border-[#2f7c67] hover:shadow-md">
                                <p class="text-xs font-semibold uppercase text-[#7f5f2a]">{{ $tool['category'] }}</p>
                                <h3 class="mt-2 text-lg font-semibold">{{ $tool['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-[#5f574c]">{{ $tool['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="bg-white py-10">
                    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                        <div>
                            <p class="text-sm font-semibold text-[#7f5f2a]">Why ToolKitly?</p>
                            <h2 class="mt-2 text-2xl font-semibold">Clean tools without friction</h2>
                            <div class="mt-5 grid gap-3 text-sm text-[#47534d] sm:grid-cols-2">
                                @foreach (['100% Free', 'No Registration', 'Fast Processing', 'Secure File Handling', 'Mobile Friendly', 'Browser-Based Tools'] as $reason)
                                    <div class="rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3 font-semibold">{{ $reason }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#7f5f2a]">How it works</p>
                            <h2 class="mt-2 text-2xl font-semibold">Three simple steps</h2>
                            <ol class="mt-5 grid gap-3 text-sm text-[#47534d]">
                                <li class="rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3"><strong>1.</strong> Upload a file or enter your data.</li>
                                <li class="rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3"><strong>2.</strong> Process instantly in your browser or securely on the server.</li>
                                <li class="rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3"><strong>3.</strong> Download or copy your result.</li>
                            </ol>
                        </div>
                    </div>
                </section>

                <section class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold text-[#7f5f2a]">New on ToolKitly</p>
                        <div class="mt-4 grid gap-2">
                            @foreach (['JWT Decoder' => '/jwt-decoder', 'Schema Generator' => '/schema-generator', 'URL Parser' => '/url-parser'] as $name => $url)
                                <a href="{{ url($url) }}" class="rounded-lg bg-[#f8faf8] px-4 py-3 text-sm font-semibold hover:text-[#2f7c67]">{{ $name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold text-[#7f5f2a]">Popular with developers</p>
                        <div class="mt-4 grid gap-2">
                            @foreach (['JSON Formatter' => '/json-formatter', 'UUID Generator' => '/uuid-generator', 'Hash Generator' => '/hash-generator'] as $name => $url)
                                <a href="{{ url($url) }}" class="rounded-lg bg-[#f8faf8] px-4 py-3 text-sm font-semibold hover:text-[#2f7c67]">{{ $name }}</a>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="bg-white py-10">
                    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                        <p class="text-sm font-semibold text-[#7f5f2a]">FAQ</p>
                        <h2 class="mt-2 text-2xl font-semibold">Common questions</h2>
                        <div class="mt-5 grid gap-3">
                            @foreach ([
                                ['Is ToolKitly free?', 'Yes. ToolKitly tools are free to use.'],
                                ['Are uploaded files stored?', 'Uploaded files are temporary and are automatically deleted after the configured cleanup window.'],
                                ['Do I need an account?', 'No. You can use the tools without registration.'],
                                ['Do tools work on mobile?', 'Yes. The interface is built to work across phones, tablets, laptops, and desktops.'],
                            ] as [$question, $answer])
                                <details class="rounded-lg border border-[#cdd8d2] bg-[#f8faf8] p-4">
                                    <summary class="cursor-pointer font-semibold">{{ $question }}</summary>
                                    <p class="mt-2 text-sm leading-6 text-[#5f574c]">{{ $answer }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="mb-5">
                        <p class="text-sm font-semibold text-[#7f5f2a]">Guides</p>
                        <h2 class="text-2xl font-semibold">Helpful starting points</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ([
                            ['How to merge PDFs online', '/pdf/merge-pdf'],
                            ['How to generate QR codes', '/qr-code-generator'],
                            ['How to format JSON', '/json-formatter'],
                        ] as [$title, $url])
                            <a href="{{ url($url) }}" class="rounded-lg border border-[#cdd8d2] bg-white p-5 text-sm font-semibold shadow-sm transition hover:border-[#2f7c67]">{{ $title }}</a>
                        @endforeach
                    </div>
                </section>
            </main>

            @include('partials.ad-slot', ['slot' => 'bottom'])
            @include('partials.footer')
        </div>

        <script type="application/json" id="toolkitly-search-data">
            {!! json_encode($tools, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
        <script>
            (() => {
                const root = document.querySelector('[data-tool-search]');
                const input = root?.querySelector('[data-tool-search-input]');
                const results = root?.querySelector('[data-tool-search-results]');
                const tools = JSON.parse(document.querySelector('#toolkitly-search-data')?.textContent || '[]');

                if (!input || !results) {
                    return;
                }

                const render = () => {
                    const query = input.value.trim().toLowerCase();
                    const matches = query
                        ? tools.filter((tool) => [tool.title, tool.description, tool.category].join(' ').toLowerCase().includes(query)).slice(0, 8)
                        : [];

                    results.classList.toggle('hidden', matches.length === 0);
                    results.innerHTML = matches.map((tool) => `
                        <a href="${tool.url}" class="block border-b border-[#eef2ef] px-4 py-3 last:border-b-0 hover:bg-[#f8faf8]">
                            <span class="block text-sm font-semibold text-[#171411]">${tool.title}</span>
                            <span class="mt-1 block text-xs text-[#655d51]">${tool.category}</span>
                        </a>
                    `).join('');
                };

                input.addEventListener('input', render);
                document.addEventListener('click', (event) => {
                    if (!root.contains(event.target)) {
                        results.classList.add('hidden');
                    }
                });
            })();
        </script>
    </body>
</html>
