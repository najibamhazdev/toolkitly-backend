<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_homepage_loads_search_first_tool_directory(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Free Online Tools for Everyday Tasks')
            ->assertSee('Search tools')
            ->assertSee('Most Used Tools')
            ->assertSee('Categories')
            ->assertSee('Featured Tools')
            ->assertSee('Why ToolKitly?')
            ->assertSee('Common questions')
            ->assertSee('/pdf/merge-pdf', false)
            ->assertSee('/json-formatter', false)
            ->assertSee('toolkitly-search-data');
    }

    public function test_footer_pages_load(): void
    {
        foreach (['/about', '/privacy-policy', '/terms', '/contact', '/tools-sitemap'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
