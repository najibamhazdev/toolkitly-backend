<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageCompressorTest extends TestCase
{
    public function test_image_compressor_page_loads(): void
    {
        $this->get('/images/image-compressor')
            ->assertOk()
            ->assertSee('Image Compressor')
            ->assertSee('/api/tools/images/image-compressor', false);
    }

    public function test_old_image_compressor_page_redirects_to_grouped_url(): void
    {
        $this->get('/image-compressor')
            ->assertRedirect('/images/image-compressor');
    }

    public function test_image_compressor_metadata_api_returns_limits_and_formats(): void
    {
        $this->getJson('/api/tools/images/image-compressor')
            ->assertOk()
            ->assertJsonPath('max_files', 20)
            ->assertJsonPath('formats.0.value', 'jpeg')
            ->assertJsonPath('formats.2.value', 'webp')
            ->assertJsonPath('defaults.quality', 72);
    }
}
