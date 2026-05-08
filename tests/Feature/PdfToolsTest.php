<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PdfToolsTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/toolkitly/tmp/pdf-tools'));

        parent::tearDown();
    }

    public function test_pdf_tool_pages_load_and_old_urls_redirect(): void
    {
        foreach (['split-pdf', 'pdf-to-jpg', 'jpg-to-pdf', 'remove-pdf-pages', 'compress-pdf', 'rotate-pdf', 'protect-pdf', 'unlock-pdf'] as $tool) {
            $this->get("/pdf/{$tool}")
                ->assertOk()
                ->assertSee("/api/tools/pdf/{$tool}", false);

            $this->get("/{$tool}")
                ->assertRedirect("/pdf/{$tool}");
        }
    }

    public function test_pdf_tool_metadata_apis_return_limits(): void
    {
        $this->getJson('/api/tools/pdf/split-pdf')
            ->assertOk()
            ->assertJsonPath('title', 'Split PDF')
            ->assertJsonPath('exports.0', 'zip');

        $this->getJson('/api/tools/pdf/pdf-to-jpg')
            ->assertOk()
            ->assertJsonPath('defaults.resolution', 150)
            ->assertJsonPath('exports.0', 'zip');

        $this->getJson('/api/tools/pdf/jpg-to-pdf')
            ->assertOk()
            ->assertJsonPath('max_files', 20)
            ->assertJsonPath('exports.0', 'pdf');

        $this->getJson('/api/tools/pdf/remove-pdf-pages')
            ->assertOk()
            ->assertJsonPath('defaults.pages', '1')
            ->assertJsonPath('exports.0', 'pdf');

        $this->getJson('/api/tools/pdf/compress-pdf')
            ->assertOk()
            ->assertJsonPath('defaults.quality', 'ebook')
            ->assertJsonPath('exports.0', 'pdf');

        $this->getJson('/api/tools/pdf/rotate-pdf')
            ->assertOk()
            ->assertJsonPath('defaults.angle', 90)
            ->assertJsonPath('exports.0', 'pdf');
    }

    public function test_split_pdf_returns_zip_download(): void
    {
        $response = $this->postJson('/api/tools/pdf/split-pdf/process', [
            'file' => $this->pdfUpload('pages.pdf', ['One', 'Two']),
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-split-pages.zip')
            ->assertJsonStructure(['file' => ['download_url', 'expires_at', 'filename', 'size']]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    public function test_jpg_to_pdf_returns_pdf_download(): void
    {
        $response = $this->postJson('/api/tools/pdf/jpg-to-pdf/process', [
            'files' => [
                $this->jpgUpload('one.jpg'),
                $this->jpgUpload('two.jpg'),
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-images.pdf')
            ->assertJsonStructure(['file' => ['download_url', 'expires_at', 'filename', 'size']]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    public function test_remove_pdf_pages_returns_pdf_download(): void
    {
        $response = $this->postJson('/api/tools/pdf/remove-pdf-pages/process', [
            'file' => $this->pdfUpload('pages.pdf', ['One', 'Two', 'Three']),
            'pages' => '2',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-pages-removed.pdf')
            ->assertJsonStructure(['file' => ['download_url', 'expires_at', 'filename', 'size']]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    public function test_pdf_to_jpg_returns_zip_download(): void
    {
        $response = $this->postJson('/api/tools/pdf/pdf-to-jpg/process', [
            'file' => $this->pdfUpload('pages.pdf', ['One']),
            'resolution' => 72,
            'quality' => 80,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-pdf-pages.zip')
            ->assertJsonStructure(['file' => ['download_url', 'expires_at', 'filename', 'size']]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    public function test_compress_pdf_returns_pdf_download(): void
    {
        $response = $this->postJson('/api/tools/pdf/compress-pdf/process', [
            'file' => $this->pdfUpload('pages.pdf', ['One']),
            'compression' => 'screen',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-compressed.pdf')
            ->assertJsonStructure(['file' => ['download_url', 'expires_at', 'filename', 'size']]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    /**
     * @param  array<int, string>  $pages
     */
    private function pdfUpload(string $name, array $pages): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'toolkitly-test-pdf-');

        $pdf = new \FPDF();

        foreach ($pages as $text) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(40, 10, $text);
        }

        $pdf->Output('F', $path);

        return new UploadedFile($path, $name, 'application/pdf', null, true);
    }

    private function jpgUpload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'toolkitly-test-jpg-');
        $image = imagecreatetruecolor(80, 60);
        $background = imagecolorallocate($image, 245, 247, 245);

        imagefill($image, 0, 0, $background);
        imagejpeg($image, $path, 85);
        imagedestroy($image);

        return new UploadedFile($path, $name, 'image/jpeg', null, true);
    }
}
