@php
    $currentTool = $currentTool ?? basename(request()->path());

    $toolsByCategory = [
        'PDF tools' => [
            'merge-pdf' => ['Merge PDF', '/pdf/merge-pdf'],
            'split-pdf' => ['Split PDF', '/pdf/split-pdf'],
            'remove-pdf-pages' => ['Remove PDF Pages', '/pdf/remove-pdf-pages'],
            'compress-pdf' => ['Compress PDF', '/pdf/compress-pdf'],
            'rotate-pdf' => ['Rotate PDF', '/pdf/rotate-pdf'],
            'pdf-to-jpg' => ['PDF to JPG', '/pdf/pdf-to-jpg'],
            'jpg-to-pdf' => ['JPG to PDF', '/pdf/jpg-to-pdf'],
            'protect-pdf' => ['Protect PDF', '/pdf/protect-pdf'],
            'unlock-pdf' => ['Unlock PDF', '/pdf/unlock-pdf'],
        ],
        'Image tools' => [
            'image-compressor' => ['Image Compressor', '/images/image-compressor'],
            'image-converter' => ['Image Converter', '/images/image-converter'],
            'image-resizer' => ['Image Resizer', '/images/image-resizer'],
            'crop-image' => ['Crop Image', '/images/crop-image'],
            'watermark-image' => ['Watermark Image', '/images/watermark-image'],
            'blur-image' => ['Blur Image', '/images/blur-image'],
            'favicon-generator' => ['Favicon Generator', '/images/favicon-generator'],
        ],
        'Developer tools' => [
            'json-formatter' => ['JSON Formatter', '/json-formatter'],
            'base64-tool' => ['Base64 Tool', '/base64-tool'],
            'jwt-decoder' => ['JWT Decoder', '/jwt-decoder'],
            'uuid-generator' => ['UUID Generator', '/uuid-generator'],
            'hash-generator' => ['Hash Generator', '/hash-generator'],
            'password-generator' => ['Password Generator', '/password-generator'],
            'token-generator' => ['Token Generator', '/token-generator'],
            'regex-tester' => ['Regex Tester', '/regex-tester'],
            'sql-formatter' => ['SQL Formatter', '/sql-formatter'],
            'xml-formatter' => ['XML Formatter', '/xml-formatter'],
            'yaml-json-converter' => ['YAML to JSON', '/yaml-json-converter'],
            'csv-to-json' => ['CSV to JSON', '/csv-to-json'],
            'unix-timestamp' => ['Unix Timestamp', '/unix-timestamp'],
            'word-counter' => ['Word Counter', '/word-counter'],
            'case-converter' => ['Case Converter', '/case-converter'],
        ],
        'Web and SEO tools' => [
            'meta-tag-generator' => ['Meta Tag Generator', '/meta-tag-generator'],
            'open-graph-preview' => ['Open Graph Preview', '/open-graph-preview'],
            'schema-generator' => ['Schema Generator', '/schema-generator'],
            'canonical-generator' => ['Canonical Generator', '/canonical-generator'],
            'redirect-checker' => ['Redirect Checker', '/redirect-checker'],
            'http-header-checker' => ['HTTP Header Checker', '/http-header-checker'],
            'robots-txt-generator' => ['Robots.txt Generator', '/robots-txt-generator'],
            'sitemap-generator' => ['Sitemap Generator', '/sitemap-generator'],
        ],
        'Design tools' => [
            'color-picker' => ['Color Picker', '/color-picker'],
            'css-gradient-generator' => ['Gradient Generator', '/css-gradient-generator'],
            'box-shadow-generator' => ['Box Shadow Generator', '/box-shadow-generator'],
        ],
        'URL tools' => [
            'short-links' => ['Short Links', '/short-links'],
            'utm-builder' => ['UTM Builder', '/utm-builder'],
            'url-encoder' => ['URL Encoder / Decoder', '/url-encoder'],
            'url-parser' => ['URL Parser', '/url-parser'],
        ],
        'Calculators' => [
            'percentage-calculator' => ['Percentage Calculator', '/percentage-calculator'],
            'age-calculator' => ['Age Calculator', '/age-calculator'],
            'date-calculator' => ['Date Calculator', '/date-calculator'],
            'unit-converter' => ['Unit Converter', '/unit-converter'],
        ],
        'Code tools' => [
            'qr-code-generator' => ['QR Code Generator', '/qr-code-generator'],
            'barcode-generator' => ['Barcode Generator', '/barcode-generator'],
        ],
    ];

    $priority = [
        'merge-pdf' => ['split-pdf', 'remove-pdf-pages', 'jpg-to-pdf', 'compress-pdf'],
        'split-pdf' => ['merge-pdf', 'remove-pdf-pages', 'compress-pdf', 'pdf-to-jpg'],
        'remove-pdf-pages' => ['merge-pdf', 'split-pdf', 'rotate-pdf', 'compress-pdf'],
        'pdf-to-jpg' => ['jpg-to-pdf', 'split-pdf', 'compress-pdf', 'merge-pdf'],
        'jpg-to-pdf' => ['pdf-to-jpg', 'merge-pdf', 'compress-pdf', 'split-pdf'],
        'json-formatter' => ['base64-tool', 'jwt-decoder', 'uuid-generator', 'hash-generator'],
        'base64-tool' => ['json-formatter', 'jwt-decoder', 'hash-generator', 'url-encoder'],
        'jwt-decoder' => ['json-formatter', 'base64-tool', 'hash-generator', 'uuid-generator'],
        'image-compressor' => ['image-resizer', 'image-converter', 'crop-image', 'favicon-generator'],
        'image-resizer' => ['image-compressor', 'crop-image', 'image-converter', 'watermark-image'],
        'qr-code-generator' => ['barcode-generator', 'short-links', 'url-encoder', 'utm-builder'],
        'barcode-generator' => ['qr-code-generator', 'uuid-generator', 'token-generator', 'url-encoder'],
    ];

    $allTools = collect($toolsByCategory)->flatMap(fn ($tools) => $tools);
    $categoryName = collect($toolsByCategory)->first(fn ($tools) => array_key_exists($currentTool, $tools), []);
    $categoryLabel = collect($toolsByCategory)->search(fn ($tools) => array_key_exists($currentTool, $tools)) ?: 'Useful tools';
    $relatedKeys = $priority[$currentTool] ?? collect($categoryName)->keys()->reject(fn ($key) => $key === $currentTool)->take(4)->values()->all();
    $relatedTools = collect($relatedKeys)
        ->map(fn ($key) => $allTools[$key] ?? null)
        ->filter()
        ->take(4);
@endphp

@if ($relatedTools->isNotEmpty())
    <aside class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8" aria-labelledby="related-tools-title">
        <div class="grid gap-4 rounded-lg border border-[#cdd8d2] bg-white p-5 shadow-sm lg:grid-cols-[220px_minmax(0,1fr)]">
            <div>
                <p class="text-xs font-semibold uppercase text-[#7f5f2a]">{{ $categoryLabel }}</p>
                <h2 id="related-tools-title" class="mt-1 text-lg font-semibold text-[#171411]">Related tools</h2>
                <p class="mt-2 text-sm leading-6 text-[#655d51]">Keep working with nearby utilities.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedTools as [$label, $url])
                    <a href="{{ url($url) }}" class="rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3 text-sm font-semibold text-[#171411] transition hover:border-[#2f7c67] hover:text-[#2f7c67]">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </aside>
@endif
