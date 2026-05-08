<meta name="robots" content="index,follow">
<meta property="og:site_name" content="ToolKitly">
<meta name="twitter:card" content="summary">
@isset($title)
    <meta name="twitter:title" content="{{ $title }} | ToolKitly">
@endisset
@isset($description)
    <meta name="twitter:description" content="{{ $description }}">
@endisset

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'ToolKitly',
    'url' => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@isset($title)
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => $title,
        'description' => $description ?? null,
        'url' => isset($path) ? url($path) : url()->current(),
        'applicationCategory' => 'UtilitiesApplication',
        'operatingSystem' => 'Any',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endisset
