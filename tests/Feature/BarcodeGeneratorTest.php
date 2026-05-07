<?php

namespace Tests\Feature;

use Tests\TestCase;

class BarcodeGeneratorTest extends TestCase
{
    public function test_barcode_generator_page_loads(): void
    {
        $this->get('/barcode-generator')
            ->assertOk()
            ->assertSee('Barcode Generator')
            ->assertSee('/api/tools/barcode-generator', false);
    }

    public function test_barcode_generator_metadata_api_returns_supported_types(): void
    {
        $this->getJson('/api/tools/barcode-generator')
            ->assertOk()
            ->assertJsonPath('types.0.value', 'code128')
            ->assertJsonPath('types.1.value', 'ean13')
            ->assertJsonPath('types.2.value', 'upc')
            ->assertJsonPath('exports.0', 'png');
    }

    public function test_barcode_generator_payload_api_formats_upc_payload(): void
    {
        $this->postJson('/api/tools/barcode-generator/payload', [
            'type' => 'upc',
            'text' => '042100005264',
            'scale' => 3,
            'height' => 12,
            'include_text' => true,
        ])
            ->assertOk()
            ->assertJsonPath('barcode.bcid', 'upca')
            ->assertJsonPath('barcode.text', '042100005264')
            ->assertJsonPath('barcode.include_text', true);
    }
}
