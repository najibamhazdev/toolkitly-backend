<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BrowserToolsTest extends TestCase
{
    public function test_browser_tool_pages_load(): void
    {
        $tools = [
            'json-formatter' => 'JSON Formatter',
            'word-counter' => 'Word Counter',
            'case-converter' => 'Case Converter',
            'regex-tester' => 'Regex Tester',
            'sql-formatter' => 'SQL Formatter',
            'xml-formatter' => 'XML Formatter',
            'yaml-json-converter' => 'YAML to JSON Converter',
            'csv-to-json' => 'CSV to JSON',
            'base64-tool' => 'Base64 Encoder / Decoder',
            'unix-timestamp' => 'Unix Timestamp',
            'jwt-decoder' => 'JWT Decoder',
            'uuid-generator' => 'UUID Generator',
            'password-generator' => 'Password Generator',
            'hash-generator' => 'Hash Generator',
            'token-generator' => 'Token Generator',
            'robots-txt-generator' => 'Robots.txt Generator',
            'sitemap-generator' => 'Sitemap Generator',
            'meta-tag-generator' => 'Meta Tag Generator',
            'open-graph-preview' => 'Open Graph Preview',
            'redirect-checker' => 'Redirect Checker',
            'http-header-checker' => 'HTTP Header Checker',
            'canonical-generator' => 'Canonical Generator',
            'schema-generator' => 'Schema Generator',
            'color-picker' => 'Color Picker',
            'css-gradient-generator' => 'CSS Gradient Generator',
            'box-shadow-generator' => 'Box Shadow Generator',
            'age-calculator' => 'Age Calculator',
            'date-calculator' => 'Date Calculator',
            'percentage-calculator' => 'Percentage Calculator',
            'unit-converter' => 'Unit Converter',
            'utm-builder' => 'UTM Builder',
            'url-encoder' => 'URL Encoder / Decoder',
            'url-parser' => 'URL Parser',
        ];

        foreach ($tools as $slug => $title) {
            $this->get('/'.$slug)
                ->assertOk()
                ->assertSee($title)
                ->assertSee('data-tool="browser-tool"', false)
                ->assertSee('data-tool-kind="'.$slug.'"', false);
        }
    }

    public function test_duplicate_tool_requests_redirect_to_existing_services(): void
    {
        $this->get('/split-pdf')->assertRedirect('/pdf/split-pdf');
        $this->get('/jpg-to-pdf')->assertRedirect('/pdf/jpg-to-pdf');
        $this->get('/pdf-to-jpg')->assertRedirect('/pdf/pdf-to-jpg');
        $this->get('/social-media-resizer')->assertRedirect('/images/image-resizer');
    }

    public function test_browser_tools_show_related_internal_links(): void
    {
        $this->get('/json-formatter')
            ->assertOk()
            ->assertSee('Related tools')
            ->assertSee('/base64-tool', false)
            ->assertSee('/jwt-decoder', false)
            ->assertSee('/uuid-generator', false);
    }

    public function test_crop_image_page_loads(): void
    {
        $this->get('/images/crop-image')
            ->assertOk()
            ->assertSee('Crop Image')
            ->assertSee('data-tool="crop-image"', false);

        $this->get('/crop-image')
            ->assertRedirect('/images/crop-image');

        $this->get('/images/watermark-image')
            ->assertOk()
            ->assertSee('Watermark Image')
            ->assertSee('data-tool="image-effect"', false);

        $this->get('/images/blur-image')
            ->assertOk()
            ->assertSee('Blur Image')
            ->assertSee('data-tool="image-effect"', false);
    }

    public function test_redirect_checker_api_returns_redirect_chain(): void
    {
        Http::fake([
            'https://example.com' => Http::response('', 301, ['Location' => 'https://example.com/final']),
            'https://example.com/final' => Http::response('ok', 200),
        ]);

        $this->postJson('/api/tools/redirect-checker', [
            'url' => 'https://example.com',
        ])
            ->assertOk()
            ->assertJsonPath('hops', 1)
            ->assertJsonPath('final_url', 'https://example.com/final')
            ->assertJsonPath('chain.0.status', 301)
            ->assertJsonPath('chain.1.status', 200);
    }

    public function test_http_header_checker_api_returns_headers(): void
    {
        Http::fake([
            'https://example.com' => Http::response('ok', 200, ['X-Test' => 'yes']),
        ]);

        $this->postJson('/api/tools/http-header-checker', [
            'url' => 'https://example.com',
        ])
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('headers.X-Test', 'yes');
    }
}
