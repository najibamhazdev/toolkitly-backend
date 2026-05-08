<?php

namespace Tests\Feature;

use App\Models\ToolEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_success_event_can_be_recorded(): void
    {
        $this->postJson('/api/analytics/tool-events', [
            'tool' => 'qr-code-generator',
            'action' => 'download',
            'status' => 'success',
            'visitor_id' => 'visitor-test',
            'metadata' => [
                'format' => 'png',
                'payload' => 'should-not-be-stored',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('ok', true);

        $event = ToolEvent::first();

        $this->assertSame('qr-code-generator', $event->tool);
        $this->assertSame('download', $event->action);
        $this->assertSame(['format' => 'png'], $event->metadata);
        $this->assertNotNull($event->visitor_hash);
        $this->assertNotNull($event->ip_hash);
    }

    public function test_analytics_dashboard_requires_token(): void
    {
        config()->set('toolkitly.analytics.dashboard_token', 'secret-token');

        $this->get('/toolkitly/analytics')->assertNotFound();
        $this->get('/toolkitly/analytics?token=wrong')->assertNotFound();
    }

    public function test_analytics_dashboard_shows_success_totals(): void
    {
        config()->set('toolkitly.analytics.dashboard_token', 'secret-token');

        ToolEvent::create([
            'tool' => 'image-compressor',
            'action' => 'generated',
            'status' => 'success',
            'visitor_hash' => 'visitor-hash',
        ]);

        $this->get('/toolkitly/analytics?token=secret-token')
            ->assertOk()
            ->assertSee('Tool success analytics')
            ->assertSee('image-compressor')
            ->assertSee('generated');
    }
}
