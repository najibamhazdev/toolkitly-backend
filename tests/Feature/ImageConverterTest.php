<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageConverterTest extends TestCase
{
    public function test_image_converter_page_loads(): void
    {
        $this->get('/images/image-converter')
            ->assertOk()
            ->assertSee('Image Converter')
            ->assertSee('/api/tools/images/image-converter', false);
    }

    public function test_old_image_converter_page_redirects_to_grouped_url(): void
    {
        $this->get('/image-converter')
            ->assertRedirect('/images/image-converter');
    }

    public function test_image_converter_metadata_api_returns_formats_and_examples(): void
    {
        $this->getJson('/api/tools/images/image-converter')
            ->assertOk()
            ->assertJsonPath('formats.0.value', 'jpeg')
            ->assertJsonPath('formats.1.value', 'png')
            ->assertJsonPath('formats.2.value', 'webp')
            ->assertJsonPath('examples.0.from', 'JPG')
            ->assertJsonPath('examples.2.to', 'JPG');
    }
}
