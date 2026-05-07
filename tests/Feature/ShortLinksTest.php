<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_links_page_loads(): void
    {
        $this->get('/short-links')
            ->assertOk()
            ->assertSee('Short Links')
            ->assertSee('/api/tools/short-links', false);
    }

    public function test_short_links_metadata_api_returns_expiration_options(): void
    {
        $this->getJson('/api/tools/short-links')
            ->assertOk()
            ->assertJsonPath('code_length', 7)
            ->assertJsonPath('expiration_options.1.value', '1_day');
    }

    public function test_short_link_can_be_created_and_redirected(): void
    {
        $response = $this->postJson('/api/tools/short-links', [
            'destination_url' => 'https://example.com/a-long-link',
            'expiration' => '7_days',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('link.destination_url', 'https://example.com/a-long-link');

        $code = $response->json('link.code');

        $this->get('/s/'.$code)
            ->assertRedirect('https://example.com/a-long-link');

        $this->assertSame(1, ShortLink::where('code', $code)->value('clicks'));
    }

    public function test_expired_short_link_returns_not_found(): void
    {
        $link = ShortLink::create([
            'code' => 'expired',
            'destination_url' => 'https://example.com',
            'expires_at' => now()->subMinute(),
        ]);

        $this->get('/s/'.$link->code)->assertNotFound();
    }
}
