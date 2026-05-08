@php
    $siteName = 'ToolKitly';
    $rawTitle = $title ?? 'Free Online PDF, QR, Developer & SEO Tools';
    $pageTitle = str_contains($rawTitle, $siteName) ? $rawTitle : "{$rawTitle} | {$siteName}";
    $metaDescription = $description ?? 'Free online tools for PDFs, images, developers, SEO, URLs, calculators, and everyday digital tasks.';
    $canonicalUrl = isset($path) ? url($path) : url()->current();
    $imageUrl = $image ?? asset('images/toolkitly-logo.png');
    $imageAlt = $imageAlt ?? 'ToolKitly logo';
    $robots = $robots ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
    $schemaType = $schemaType ?? ((isset($path) && $path === '/') ? 'WebSite' : 'WebApplication');
    $schemaContextKey = chr(64).'context';
    $schemaTypeKey = chr(64).'type';
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="{{ $robots }}">
<meta name="theme-color" content="#101820">
<meta name="description" content="{{ $metaDescription }}">
@isset($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endisset

<title>{{ $pageTitle }}</title>
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:image:alt" content="{{ $imageAlt }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
<meta name="twitter:image:alt" content="{{ $imageAlt }}">

<script type="application/ld+json">
{!! json_encode([
    $schemaContextKey => 'https://schema.org',
    $schemaTypeKey => 'WebSite',
    'name' => $siteName,
    'url' => url('/'),
    'potentialAction' => [
        $schemaTypeKey => 'SearchAction',
        'target' => url('/').'?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<script type="application/ld+json">
{!! json_encode(array_filter([
    $schemaContextKey => 'https://schema.org',
    $schemaTypeKey => $schemaType,
    'name' => $rawTitle,
    'description' => $metaDescription,
    'url' => $canonicalUrl,
    'applicationCategory' => $schemaType === 'WebApplication' ? 'UtilitiesApplication' : null,
    'operatingSystem' => $schemaType === 'WebApplication' ? 'Any' : null,
    'browserRequirements' => $schemaType === 'WebApplication' ? 'Requires JavaScript' : null,
    'offers' => $schemaType === 'WebApplication' ? [
        $schemaTypeKey => 'Offer',
        'price' => '0',
        'priceCurrency' => 'USD',
    ] : null,
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
