<header class="border-b border-[#d9e1dc] bg-white/90">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/toolkitly-logo.png') }}" alt="ToolKitly" class="h-10 w-44 object-contain object-left lg:h-14 lg:w-60">
            </a>

            <button
                type="button"
                class="rounded-lg border border-[#cdd8d2] px-3 py-2 text-sm font-semibold text-[#47534d] transition hover:border-[#6d8d80] hover:text-[#171411] lg:hidden"
                data-toolkitly-navigation-toggle
                aria-controls="toolkitly-primary-navigation"
                aria-expanded="false"
            >
                Menu
            </button>
        </div>

        @php
            $link = 'block rounded-lg px-3 py-2 text-sm font-medium text-[#47534d] transition hover:bg-[#f4f7f5] hover:text-[#171411]';
            $active = 'bg-[#2f7c67] text-white hover:bg-[#2f7c67] hover:text-white';
            $button = 'rounded-lg border border-[#cdd8d2] px-3 py-2 text-sm font-semibold text-[#47534d] transition hover:border-[#6d8d80] hover:text-[#171411]';
            $heading = 'px-3 pt-2 pb-1 text-xs font-semibold uppercase text-[#7f5f2a]';

            $codeRoutes = ['tools.qr-code-generator', 'tools.barcode-generator'];
            $pdfRoutes = ['tools.merge-pdf', 'tools.split-pdf', 'tools.pdf-to-jpg', 'tools.jpg-to-pdf', 'tools.remove-pdf-pages', 'tools.compress-pdf', 'tools.rotate-pdf', 'tools.protect-pdf', 'tools.unlock-pdf'];
            $imageRoutes = ['tools.crop-image', 'tools.watermark-image', 'tools.blur-image', 'tools.image-compressor', 'tools.image-converter', 'tools.image-resizer', 'tools.favicon-generator'];
            $developerRoutes = ['tools.json-formatter', 'tools.word-counter', 'tools.case-converter', 'tools.regex-tester', 'tools.sql-formatter', 'tools.xml-formatter', 'tools.yaml-json-converter', 'tools.csv-to-json', 'tools.base64-tool', 'tools.unix-timestamp', 'tools.jwt-decoder', 'tools.uuid-generator', 'tools.password-generator', 'tools.hash-generator', 'tools.token-generator'];
            $seoRoutes = ['tools.robots-txt-generator', 'tools.sitemap-generator', 'tools.meta-tag-generator', 'tools.open-graph-preview', 'tools.redirect-checker', 'tools.http-header-checker', 'tools.canonical-generator', 'tools.schema-generator'];
            $designRoutes = ['tools.color-picker', 'tools.css-gradient-generator', 'tools.box-shadow-generator'];
            $calculatorRoutes = ['tools.age-calculator', 'tools.date-calculator', 'tools.percentage-calculator', 'tools.unit-converter'];
            $urlRoutes = ['tools.short-links', 'tools.utm-builder', 'tools.url-encoder', 'tools.url-parser'];
        @endphp

        <nav id="toolkitly-primary-navigation" aria-label="Primary navigation" class="toolkitly-nav mt-4 hidden gap-2 lg:mt-0 lg:flex lg:flex-wrap lg:items-center lg:justify-end" data-toolkitly-navigation>
            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$codeRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Codes</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg" data-menu-size="narrow">
                    <a class="{{ $link }} {{ request()->routeIs('tools.qr-code-generator') ? $active : '' }}" href="{{ route('tools.qr-code-generator') }}">QR Code Generator</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.barcode-generator') ? $active : '' }}" href="{{ route('tools.barcode-generator') }}">Barcode Generator</a>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$pdfRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">PDF</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg sm:grid-cols-2" data-menu-size="wide">
                    <div>
                        <p class="{{ $heading }}">Organize</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.merge-pdf') ? $active : '' }}" href="{{ route('tools.merge-pdf') }}">Merge PDF</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.split-pdf') ? $active : '' }}" href="{{ route('tools.split-pdf') }}">Split PDF</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.remove-pdf-pages') ? $active : '' }}" href="{{ route('tools.remove-pdf-pages') }}">Remove Pages</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.rotate-pdf') ? $active : '' }}" href="{{ route('tools.rotate-pdf') }}">Rotate PDF</a>
                    </div>
                    <div>
                        <p class="{{ $heading }}">Convert & secure</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.compress-pdf') ? $active : '' }}" href="{{ route('tools.compress-pdf') }}">Compress PDF</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.pdf-to-jpg') ? $active : '' }}" href="{{ route('tools.pdf-to-jpg') }}">PDF to JPG</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.jpg-to-pdf') ? $active : '' }}" href="{{ route('tools.jpg-to-pdf') }}">JPG to PDF</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.protect-pdf') ? $active : '' }}" href="{{ route('tools.protect-pdf') }}">Protect PDF</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.unlock-pdf') ? $active : '' }}" href="{{ route('tools.unlock-pdf') }}">Unlock PDF</a>
                    </div>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$imageRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Images</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg sm:grid-cols-2" data-menu-size="wide">
                    <div>
                        <p class="{{ $heading }}">Edit</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.crop-image') ? $active : '' }}" href="{{ route('tools.crop-image') }}">Crop Image</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.watermark-image') ? $active : '' }}" href="{{ route('tools.watermark-image') }}">Watermark Image</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.blur-image') ? $active : '' }}" href="{{ route('tools.blur-image') }}">Blur Image</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.image-resizer') ? $active : '' }}" href="{{ route('tools.image-resizer') }}">Social Media Resizer</a>
                    </div>
                    <div>
                        <p class="{{ $heading }}">Optimize & convert</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.image-compressor') ? $active : '' }}" href="{{ route('tools.image-compressor') }}">Image Compressor</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.image-converter') ? $active : '' }}" href="{{ route('tools.image-converter') }}">Image Converter</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.image-resizer') ? $active : '' }}" href="{{ route('tools.image-resizer') }}">Image Resizer</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.favicon-generator') ? $active : '' }}" href="{{ route('tools.favicon-generator') }}">Favicon Generator</a>
                    </div>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$developerRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Text & Dev</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg sm:grid-cols-2 lg:grid-cols-3" data-menu-size="mega">
                    <div>
                        <p class="{{ $heading }}">Text</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.word-counter') ? $active : '' }}" href="{{ route('tools.word-counter') }}">Word Counter</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.case-converter') ? $active : '' }}" href="{{ route('tools.case-converter') }}">Case Converter</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.base64-tool') ? $active : '' }}" href="{{ route('tools.base64-tool') }}">Base64 Tool</a>
                    </div>
                    <div>
                        <p class="{{ $heading }}">Code data</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.json-formatter') ? $active : '' }}" href="{{ route('tools.json-formatter') }}">JSON Formatter</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.xml-formatter') ? $active : '' }}" href="{{ route('tools.xml-formatter') }}">XML Formatter</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.yaml-json-converter') ? $active : '' }}" href="{{ route('tools.yaml-json-converter') }}">YAML to JSON</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.csv-to-json') ? $active : '' }}" href="{{ route('tools.csv-to-json') }}">CSV to JSON</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.sql-formatter') ? $active : '' }}" href="{{ route('tools.sql-formatter') }}">SQL Formatter</a>
                    </div>
                    <div>
                        <p class="{{ $heading }}">Security & IDs</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.regex-tester') ? $active : '' }}" href="{{ route('tools.regex-tester') }}">Regex Tester</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.jwt-decoder') ? $active : '' }}" href="{{ route('tools.jwt-decoder') }}">JWT Decoder</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.hash-generator') ? $active : '' }}" href="{{ route('tools.hash-generator') }}">Hash Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.uuid-generator') ? $active : '' }}" href="{{ route('tools.uuid-generator') }}">UUID Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.password-generator') ? $active : '' }}" href="{{ route('tools.password-generator') }}">Password Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.token-generator') ? $active : '' }}" href="{{ route('tools.token-generator') }}">Token Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.unix-timestamp') ? $active : '' }}" href="{{ route('tools.unix-timestamp') }}">Unix Timestamp</a>
                    </div>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$seoRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Web & SEO</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg sm:grid-cols-2" data-menu-size="wide">
                    <div>
                        <p class="{{ $heading }}">SEO tags</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.meta-tag-generator') ? $active : '' }}" href="{{ route('tools.meta-tag-generator') }}">Meta Tag Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.open-graph-preview') ? $active : '' }}" href="{{ route('tools.open-graph-preview') }}">Open Graph Preview</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.canonical-generator') ? $active : '' }}" href="{{ route('tools.canonical-generator') }}">Canonical Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.schema-generator') ? $active : '' }}" href="{{ route('tools.schema-generator') }}">Schema Generator</a>
                    </div>
                    <div>
                        <p class="{{ $heading }}">Site checks</p>
                        <a class="{{ $link }} {{ request()->routeIs('tools.robots-txt-generator') ? $active : '' }}" href="{{ route('tools.robots-txt-generator') }}">Robots.txt Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.sitemap-generator') ? $active : '' }}" href="{{ route('tools.sitemap-generator') }}">Sitemap Generator</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.redirect-checker') ? $active : '' }}" href="{{ route('tools.redirect-checker') }}">Redirect Checker</a>
                        <a class="{{ $link }} {{ request()->routeIs('tools.http-header-checker') ? $active : '' }}" href="{{ route('tools.http-header-checker') }}">HTTP Header Checker</a>
                    </div>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$designRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Design</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg" data-menu-size="medium">
                    <a class="{{ $link }} {{ request()->routeIs('tools.color-picker') ? $active : '' }}" href="{{ route('tools.color-picker') }}">Color Picker</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.css-gradient-generator') ? $active : '' }}" href="{{ route('tools.css-gradient-generator') }}">Gradient Generator</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.box-shadow-generator') ? $active : '' }}" href="{{ route('tools.box-shadow-generator') }}">Box Shadow Generator</a>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$calculatorRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">Calculators</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg" data-menu-size="medium">
                    <a class="{{ $link }} {{ request()->routeIs('tools.age-calculator') ? $active : '' }}" href="{{ route('tools.age-calculator') }}">Age Calculator</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.date-calculator') ? $active : '' }}" href="{{ route('tools.date-calculator') }}">Date Calculator</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.percentage-calculator') ? $active : '' }}" href="{{ route('tools.percentage-calculator') }}">Percentage Calculator</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.unit-converter') ? $active : '' }}" href="{{ route('tools.unit-converter') }}">Unit Converter</a>
                </div>
            </details>

            <details class="shrink-0 lg:relative" data-toolkitly-menu>
                <summary class="{{ $button }} list-none whitespace-nowrap cursor-pointer {{ request()->routeIs(...$urlRoutes) ? 'border-[#2f7c67] bg-[#2f7c67] text-white hover:text-white' : '' }}">URL</summary>
                <div class="toolkitly-menu-panel grid gap-1 rounded-lg border border-[#cdd8d2] bg-white p-2 shadow-lg" data-menu-size="medium">
                    <a class="{{ $link }} {{ request()->routeIs('tools.short-links') ? $active : '' }}" href="{{ route('tools.short-links') }}">Short Links</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.utm-builder') ? $active : '' }}" href="{{ route('tools.utm-builder') }}">UTM Builder</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.url-encoder') ? $active : '' }}" href="{{ route('tools.url-encoder') }}">URL Encoder</a>
                    <a class="{{ $link }} {{ request()->routeIs('tools.url-parser') ? $active : '' }}" href="{{ route('tools.url-parser') }}">URL Parser</a>
                </div>
            </details>
        </nav>
    </div>
</header>

<script>
    document.querySelectorAll('[data-toolkitly-navigation]').forEach((navigation) => {
        const toggle = document.querySelector(`[data-toolkitly-navigation-toggle][aria-controls="${navigation.id}"]`);
        const menus = Array.from(navigation.querySelectorAll('[data-toolkitly-menu]'));

        toggle?.addEventListener('click', () => {
            const isOpen = !navigation.classList.contains('hidden');

            navigation.classList.toggle('hidden', isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));

            if (isOpen) {
                menus.forEach((menu) => menu.removeAttribute('open'));
            }
        });

        menus.forEach((menu) => {
            menu.addEventListener('toggle', () => {
                if (!menu.open) {
                    return;
                }

                menus.forEach((otherMenu) => {
                    if (otherMenu !== menu) {
                        otherMenu.removeAttribute('open');
                    }
                });
            });
        });

        document.addEventListener('click', (event) => {
            if (navigation.contains(event.target) || toggle?.contains(event.target)) {
                return;
            }

            menus.forEach((menu) => menu.removeAttribute('open'));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            menus.forEach((menu) => menu.removeAttribute('open'));
            navigation.classList.add('hidden');
            toggle?.setAttribute('aria-expanded', 'false');
        });
    });
</script>
