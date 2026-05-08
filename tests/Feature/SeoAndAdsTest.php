<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoAndAdsTest extends TestCase
{
    public function test_robots_txt_points_to_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('User-agent: *')
            ->assertSee('Allow: /')
            ->assertSee('Sitemap: http://localhost/sitemap.xml');
    }

    public function test_sitemap_lists_canonical_tool_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee('http://localhost')
            ->assertSee('http://localhost/qr-code-generator')
            ->assertSee('http://localhost/pdf/merge-pdf')
            ->assertSee('http://localhost/images/image-compressor')
            ->assertSee('http://localhost/json-formatter')
            ->assertDontSee('http://localhost/merge-pdf')
            ->assertDontSee('/api/');
    }

    public function test_adsense_is_disabled_until_configured(): void
    {
        $this->get('/json-formatter')
            ->assertOk()
            ->assertDontSee('pagead2.googlesyndication.com')
            ->assertDontSee('adsbygoogle');
    }

    public function test_adsense_script_and_slots_render_when_configured(): void
    {
        config()->set('toolkitly.adsense.client', 'ca-pub-test');
        config()->set('toolkitly.adsense.slots.top', '1111111111');
        config()->set('toolkitly.adsense.slots.bottom', '2222222222');

        $this->get('/json-formatter')
            ->assertOk()
            ->assertSee('pagead2.googlesyndication.com', false)
            ->assertSee('data-ad-client="ca-pub-test"', false)
            ->assertSee('data-ad-slot="1111111111"', false)
            ->assertSee('data-ad-slot="2222222222"', false);
    }
}
