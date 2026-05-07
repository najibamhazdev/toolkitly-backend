<?php

namespace Tests\Feature;

use Tests\TestCase;

class QRCodeGeneratorTest extends TestCase
{
    public function test_qr_code_generator_page_loads(): void
    {
        $this->get('/qr-code-generator')
            ->assertOk()
            ->assertSee('QR Code Generator')
            ->assertSee('/api/tools/qr-code-generator', false);
    }

    public function test_qr_code_generator_metadata_api_returns_supported_types(): void
    {
        $this->getJson('/api/tools/qr-code-generator')
            ->assertOk()
            ->assertJsonPath('types.0.value', 'url')
            ->assertJsonPath('exports.0', 'png');
    }

    public function test_qr_code_generator_payload_api_formats_wifi_payload(): void
    {
        $this->postJson('/api/tools/qr-code-generator/payload', [
            'type' => 'wifi',
            'wifi' => [
                'ssid' => 'ToolKitly Guest',
                'password' => 'free-tools',
                'encryption' => 'WPA',
                'hidden' => false,
            ],
        ])
            ->assertOk()
            ->assertJsonPath('payload', 'WIFI:T:WPA;S:ToolKitly Guest;P:free-tools;H:false;;');
    }
}
