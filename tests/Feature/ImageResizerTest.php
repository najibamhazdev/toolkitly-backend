<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageResizerTest extends TestCase
{
    public function test_image_resizer_page_loads(): void
    {
        $this->get('/images/image-resizer')
            ->assertOk()
            ->assertSee('Image Resizer')
            ->assertSee('/api/tools/images/image-resizer', false);
    }

    public function test_old_image_resizer_page_redirects_to_grouped_url(): void
    {
        $this->get('/image-resizer')
            ->assertRedirect('/images/image-resizer');
    }

    public function test_image_resizer_metadata_api_returns_social_presets(): void
    {
        $this->getJson('/api/tools/images/image-resizer')
            ->assertOk()
            ->assertJsonPath('presets.0.group', 'Instagram')
            ->assertJsonPath('presets.1.group', 'Facebook')
            ->assertJsonPath('presets.2.group', 'LinkedIn')
            ->assertJsonPath('defaults.fit', 'cover');
    }
}
