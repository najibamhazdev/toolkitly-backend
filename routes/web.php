<?php

use App\Http\Controllers\Api\MergePdfController;
use App\Http\Controllers\Api\PdfToolController;
use App\Http\Controllers\ShortLinkRedirectController;
use App\Http\Controllers\ToolAnalyticsController;
use App\Http\Controllers\ToolSettingsController;
use Illuminate\Support\Facades\Route;

$homepageTools = [
    ['title' => 'QR Code Generator', 'description' => 'Create QR codes for URLs, Wi-Fi, business cards, and text.', 'url' => '/qr-code-generator', 'category' => 'Code tools'],
    ['title' => 'Barcode Generator', 'description' => 'Generate Code128, EAN-13, and UPC barcodes.', 'url' => '/barcode-generator', 'category' => 'Code tools'],
    ['title' => 'Merge PDF', 'description' => 'Combine multiple PDF files into one document.', 'url' => '/pdf/merge-pdf', 'category' => 'PDF tools'],
    ['title' => 'Split PDF', 'description' => 'Split a PDF into separate files.', 'url' => '/pdf/split-pdf', 'category' => 'PDF tools'],
    ['title' => 'PDF to JPG', 'description' => 'Convert PDF pages into JPG images.', 'url' => '/pdf/pdf-to-jpg', 'category' => 'PDF tools'],
    ['title' => 'JPG to PDF', 'description' => 'Create a PDF from JPG images.', 'url' => '/pdf/jpg-to-pdf', 'category' => 'PDF tools'],
    ['title' => 'Compress PDF', 'description' => 'Reduce PDF file size online.', 'url' => '/pdf/compress-pdf', 'category' => 'PDF tools'],
    ['title' => 'Image Compressor', 'description' => 'Compress images in your browser.', 'url' => '/images/image-compressor', 'category' => 'Image tools'],
    ['title' => 'Image Converter', 'description' => 'Convert JPG, PNG, and WebP formats.', 'url' => '/images/image-converter', 'category' => 'Image tools'],
    ['title' => 'Image Resizer', 'description' => 'Resize images for Instagram, Facebook, LinkedIn, and more.', 'url' => '/images/image-resizer', 'category' => 'Image tools'],
    ['title' => 'Favicon Generator', 'description' => 'Generate favicons and app icons.', 'url' => '/images/favicon-generator', 'category' => 'Image tools'],
    ['title' => 'JSON Formatter', 'description' => 'Format, validate, and minify JSON instantly.', 'url' => '/json-formatter', 'category' => 'Developer tools'],
    ['title' => 'Password Generator', 'description' => 'Create strong random passwords locally.', 'url' => '/password-generator', 'category' => 'Developer tools'],
    ['title' => 'UUID Generator', 'description' => 'Generate UUID v4 identifiers.', 'url' => '/uuid-generator', 'category' => 'Developer tools'],
    ['title' => 'Hash Generator', 'description' => 'Generate MD5 and SHA-256 hashes.', 'url' => '/hash-generator', 'category' => 'Developer tools'],
    ['title' => 'JWT Decoder', 'description' => 'Decode JWT headers and payloads locally.', 'url' => '/jwt-decoder', 'category' => 'Developer tools'],
    ['title' => 'Meta Tag Generator', 'description' => 'Generate SEO meta tags for pages.', 'url' => '/meta-tag-generator', 'category' => 'Web and SEO tools'],
    ['title' => 'Schema Generator', 'description' => 'Create JSON-LD schema markup.', 'url' => '/schema-generator', 'category' => 'Web and SEO tools'],
    ['title' => 'Redirect Checker', 'description' => 'Inspect a URL redirect chain.', 'url' => '/redirect-checker', 'category' => 'Web and SEO tools'],
    ['title' => 'UTM Builder', 'description' => 'Build campaign URLs with tracking parameters.', 'url' => '/utm-builder', 'category' => 'URL tools'],
    ['title' => 'URL Encoder / Decoder', 'description' => 'Encode and decode URL strings safely.', 'url' => '/url-encoder', 'category' => 'URL tools'],
    ['title' => 'Color Picker', 'description' => 'Pick colors and convert HEX, RGB, and HSL.', 'url' => '/color-picker', 'category' => 'Design tools'],
    ['title' => 'Age Calculator', 'description' => 'Calculate age between dates.', 'url' => '/age-calculator', 'category' => 'Calculators'],
    ['title' => 'Percentage Calculator', 'description' => 'Calculate percentages and changes.', 'url' => '/percentage-calculator', 'category' => 'Calculators'],
];

$toolsSitemapContent = '<div class="grid gap-2">'.collect($homepageTools)
    ->map(fn ($tool) => '<a class="block rounded-lg border border-[#d9e1dc] bg-[#f8faf8] px-4 py-3 font-semibold text-[#171411] hover:text-[#2f7c67]" href="'.e(url($tool['url'])).'">'.e($tool['title']).'<span class="mt-1 block text-xs font-normal text-[#655d51]">'.e($tool['category']).'</span></a>')
    ->implode('').'</div>';

Route::view('/', 'home', ['tools' => $homepageTools])->name('home');

Route::view('/about', 'pages.simple', [
    'title' => 'About',
    'description' => 'Learn about ToolKitly, a free online toolbox for everyday digital tasks.',
    'path' => '/about',
    'content' => '<p>ToolKitly is a free online utilities platform built for everyday users, developers, students, designers, and businesses. The goal is simple: fast tools, clean pages, mobile-friendly workflows, and no account required.</p>',
])->name('pages.about');

Route::view('/privacy-policy', 'pages.simple', [
    'title' => 'Privacy Policy',
    'description' => 'ToolKitly privacy policy for free online tools.',
    'path' => '/privacy-policy',
    'content' => '<p>ToolKitly is designed to collect as little information as possible. Browser-based tools process data locally in your browser. Server-side file tools use temporary files for processing, and uploaded files are intended to be automatically deleted after the configured cleanup window.</p>',
])->name('pages.privacy-policy');

Route::view('/terms', 'pages.simple', [
    'title' => 'Terms',
    'description' => 'Terms of use for ToolKitly free online tools.',
    'path' => '/terms',
    'content' => '<p>ToolKitly tools are provided free of charge for general utility use. Please verify important output before relying on it, and do not upload files you do not have permission to process.</p>',
])->name('pages.terms');

Route::view('/contact', 'pages.simple', [
    'title' => 'Contact',
    'description' => 'Contact ToolKitly for feedback, suggestions, and support.',
    'path' => '/contact',
    'content' => '<p>For feedback, bug reports, or tool suggestions, contact the ToolKitly team at <a class="font-semibold text-[#2f7c67]" href="mailto:hello@toolkitly.net">hello@toolkitly.net</a>.</p>',
])->name('pages.contact');

Route::view('/tools-sitemap', 'pages.simple', [
    'title' => 'Tools Sitemap',
    'description' => 'Browse ToolKitly tools by category.',
    'path' => '/tools-sitemap',
    'content' => $toolsSitemapContent,
])->name('pages.tools-sitemap');

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n",
        200,
        ['Content-Type' => 'text/plain']
    );
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = collect(Route::getRoutes())
        ->filter(fn ($route) => in_array('GET', $route->methods(), true))
        ->filter(fn ($route) => $route->getName() && str_starts_with($route->getName(), 'tools.'))
        ->reject(fn ($route) => str_contains($route->uri(), '{'))
        ->map(fn ($route) => url($route->uri()))
        ->push(url('/'))
        ->unique()
        ->sort()
        ->values();

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::view('/qr-code-generator', 'tools.qr-code-generator')->name('tools.qr-code-generator');
Route::view('/barcode-generator', 'tools.barcode-generator')->name('tools.barcode-generator');
Route::view('/short-links', 'tools.short-links')->name('tools.short-links');

$browserTools = [
    ['json-formatter', 'JSON Formatter', 'Format, validate, and minify JSON in your browser.'],
    ['word-counter', 'Word Counter', 'Count words, characters, sentences, and paragraphs instantly.'],
    ['case-converter', 'Case Converter', 'Convert text between uppercase, lowercase, title case, camel case, and more.'],
    ['regex-tester', 'Regex Tester', 'Test regular expressions against sample text in your browser.'],
    ['sql-formatter', 'SQL Formatter', 'Format SQL queries for easier reading.'],
    ['xml-formatter', 'XML Formatter', 'Format and validate XML documents in your browser.'],
    ['yaml-json-converter', 'YAML to JSON Converter', 'Convert simple YAML to JSON and JSON back to YAML.'],
    ['csv-to-json', 'CSV to JSON', 'Convert CSV rows into JSON objects.'],
    ['base64-tool', 'Base64 Encoder / Decoder', 'Encode and decode Base64 text in your browser.'],
    ['unix-timestamp', 'Unix Timestamp', 'Convert Unix timestamps to dates and dates back to Unix time.'],
    ['jwt-decoder', 'JWT Decoder', 'Decode JWT headers and payloads without sending tokens to a server.'],
    ['uuid-generator', 'UUID Generator', 'Generate UUID v4 identifiers instantly.'],
    ['password-generator', 'Password Generator', 'Create strong random passwords locally in your browser.'],
    ['hash-generator', 'Hash Generator', 'Generate MD5 and SHA-256 hashes from text.'],
    ['token-generator', 'Token Generator', 'Generate secure random tokens for development and testing.'],
    ['robots-txt-generator', 'Robots.txt Generator', 'Generate a robots.txt file for your website.'],
    ['sitemap-generator', 'Sitemap Generator', 'Create a simple XML sitemap from a list of URLs.'],
    ['meta-tag-generator', 'Meta Tag Generator', 'Generate SEO meta tags for a web page.'],
    ['open-graph-preview', 'Open Graph Preview', 'Preview social sharing cards and Open Graph metadata.'],
    ['redirect-checker', 'Redirect Checker', 'Check URL redirects and inspect the HTTP redirect chain.', '/api/tools/redirect-checker'],
    ['http-header-checker', 'HTTP Header Checker', 'Check HTTP response headers for a URL.', '/api/tools/http-header-checker'],
    ['canonical-generator', 'Canonical Generator', 'Generate canonical link tags for web pages.'],
    ['schema-generator', 'Schema Generator', 'Generate JSON-LD schema markup for common website entities.'],
    ['color-picker', 'Color Picker', 'Pick colors and convert between HEX, RGB, and HSL.'],
    ['css-gradient-generator', 'CSS Gradient Generator', 'Create CSS linear gradients with live preview.'],
    ['box-shadow-generator', 'Box Shadow Generator', 'Design CSS box shadows with live preview.'],
    ['age-calculator', 'Age Calculator', 'Calculate age from a birth date in years, months, and days.'],
    ['date-calculator', 'Date Calculator', 'Add or subtract days from a date and compare two dates.'],
    ['percentage-calculator', 'Percentage Calculator', 'Calculate percentages, increases, decreases, and differences.'],
    ['unit-converter', 'Unit Converter', 'Convert common length, weight, and temperature units.'],
    ['utm-builder', 'UTM Builder', 'Build campaign URLs with UTM tracking parameters.'],
    ['url-encoder', 'URL Encoder / Decoder', 'Encode and decode URL strings safely.'],
    ['url-parser', 'URL Parser', 'Parse URLs into protocol, host, path, query, and fragment parts.'],
];

foreach ($browserTools as $browserTool) {
    [$slug, $title, $description] = $browserTool;

    Route::view('/'.$slug, 'tools.browser-tool', [
        'toolKind' => $slug,
        'title' => $title,
        'description' => $description,
        'path' => '/'.$slug,
        'actionUrl' => $browserTool[3] ?? null,
    ])->name('tools.'.$slug);
}

Route::prefix('pdf')->group(function (): void {
    Route::view('/merge-pdf', 'tools.merge-pdf')->name('tools.merge-pdf');
    Route::get('/merge-pdf/download/{id}', [MergePdfController::class, 'download'])->name('tools.merge-pdf.download');
    Route::view('/split-pdf', 'tools.split-pdf')->name('tools.split-pdf');
    Route::view('/pdf-to-jpg', 'tools.pdf-to-jpg')->name('tools.pdf-to-jpg');
    Route::view('/jpg-to-pdf', 'tools.jpg-to-pdf')->name('tools.jpg-to-pdf');
    Route::view('/remove-pdf-pages', 'tools.remove-pdf-pages')->name('tools.remove-pdf-pages');
    Route::view('/compress-pdf', 'tools.compress-pdf')->name('tools.compress-pdf');
    Route::view('/rotate-pdf', 'tools.rotate-pdf')->name('tools.rotate-pdf');
    Route::view('/protect-pdf', 'tools.protect-pdf')->name('tools.protect-pdf');
    Route::view('/unlock-pdf', 'tools.unlock-pdf')->name('tools.unlock-pdf');
    Route::get('/{tool}/download/{id}/{filename}', [PdfToolController::class, 'download'])->name('tools.pdf.download');
});

Route::prefix('images')->group(function (): void {
    Route::view('/crop-image', 'tools.crop-image')->name('tools.crop-image');
    Route::view('/watermark-image', 'tools.watermark-image')->name('tools.watermark-image');
    Route::view('/blur-image', 'tools.blur-image')->name('tools.blur-image');
    Route::view('/image-compressor', 'tools.image-compressor')->name('tools.image-compressor');
    Route::view('/image-converter', 'tools.image-converter')->name('tools.image-converter');
    Route::view('/image-resizer', 'tools.image-resizer')->name('tools.image-resizer');
    Route::view('/favicon-generator', 'tools.favicon-generator')->name('tools.favicon-generator');
});

Route::redirect('/merge-pdf', '/pdf/merge-pdf', 301);
Route::redirect('/split-pdf', '/pdf/split-pdf', 301);
Route::redirect('/pdf-to-jpg', '/pdf/pdf-to-jpg', 301);
Route::redirect('/jpg-to-pdf', '/pdf/jpg-to-pdf', 301);
Route::redirect('/remove-pdf-pages', '/pdf/remove-pdf-pages', 301);
Route::redirect('/compress-pdf', '/pdf/compress-pdf', 301);
Route::redirect('/rotate-pdf', '/pdf/rotate-pdf', 301);
Route::redirect('/protect-pdf', '/pdf/protect-pdf', 301);
Route::redirect('/unlock-pdf', '/pdf/unlock-pdf', 301);
Route::redirect('/crop-image', '/images/crop-image', 301);
Route::redirect('/watermark-image', '/images/watermark-image', 301);
Route::redirect('/blur-image', '/images/blur-image', 301);
Route::redirect('/image-compressor', '/images/image-compressor', 301);
Route::redirect('/image-converter', '/images/image-converter', 301);
Route::redirect('/image-resizer', '/images/image-resizer', 301);
Route::redirect('/social-media-resizer', '/images/image-resizer', 301);
Route::redirect('/images/social-media-resizer', '/images/image-resizer', 301);
Route::redirect('/favicon-generator', '/images/favicon-generator', 301);

Route::get('/s/{code}', ShortLinkRedirectController::class)->name('short-links.redirect');
Route::get('/toolkitly/analytics', ToolAnalyticsController::class)->name('analytics.tool-events');
Route::get('/toolkitly/settings', [ToolSettingsController::class, 'edit'])->name('admin.settings');
Route::put('/toolkitly/settings', [ToolSettingsController::class, 'update'])->name('admin.settings.update');
