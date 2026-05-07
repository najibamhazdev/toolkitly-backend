<?php

namespace Tests\Feature;

use Tests\TestCase;

class FaviconGeneratorTest extends TestCase
{
    public function test_favicon_generator_page_loads(): void
    {
        $this->get('/images/favicon-generator')
            ->assertOk()
            ->assertSee('Favicon Generator')
            ->assertSee('/api/tools/images/favicon-generator', false);
    }

    public function test_old_favicon_generator_page_redirects_to_grouped_url(): void
    {
        $this->get('/favicon-generator')
            ->assertRedirect('/images/favicon-generator');
    }

    public function test_favicon_generator_metadata_api_returns_sizes(): void
    {
        $this->getJson('/api/tools/images/favicon-generator')
            ->assertOk()
            ->assertJsonPath('sizes.0.size', 16)
            ->assertJsonPath('sizes.4.size', 512)
            ->assertJsonPath('ico_sizes.0', 16);
    }
}
