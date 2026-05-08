<?php

namespace Tests\Feature;

use App\Models\PlatformSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_requires_token(): void
    {
        config()->set('toolkitly.admin.settings_token', 'settings-token');

        $this->get('/toolkitly/settings')->assertNotFound();
        $this->get('/toolkitly/settings?token=wrong')->assertNotFound();
    }

    public function test_settings_page_updates_ads_and_analytics_values(): void
    {
        config()->set('toolkitly.admin.settings_token', 'settings-token');

        $this->put('/toolkitly/settings?token=settings-token', [
            'google_analytics_measurement_id' => 'G-ABC1234567',
            'google_adsense_client' => 'ca-pub-1234567890123456',
            'google_adsense_slot_top' => '1111111111',
            'google_adsense_slot_middle' => '2222222222',
            'google_adsense_slot_bottom' => '3333333333',
            'max_upload_kb' => 20480,
            'temporary_file_ttl' => 7200,
        ])->assertRedirect('/toolkitly/settings?token=settings-token');

        $this->assertSame('G-ABC1234567', PlatformSetting::where('key', 'google_analytics_measurement_id')->value('value'));
        $this->assertSame('ca-pub-1234567890123456', PlatformSetting::where('key', 'google_adsense_client')->value('value'));

        $this->get('/qr-code-generator')
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-ABC1234567', false)
            ->assertSee('pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567890123456', false)
            ->assertSee('data-ad-slot="1111111111"', false)
            ->assertSee('data-ad-slot="3333333333"', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-ad-slot="2222222222"', false);
    }
}
