<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MergePdfTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/toolkitly/tmp/merge-pdf'));

        parent::tearDown();
    }

    public function test_merge_pdf_page_loads(): void
    {
        $this->get('/pdf/merge-pdf')
            ->assertOk()
            ->assertSee('Merge PDF')
            ->assertSee('/api/tools/pdf/merge-pdf', false)
            ->assertSee('Related tools')
            ->assertSee('/pdf/split-pdf', false)
            ->assertSee('/pdf/remove-pdf-pages', false)
            ->assertSee('/pdf/jpg-to-pdf', false);
    }

    public function test_old_merge_pdf_page_redirects_to_grouped_url(): void
    {
        $this->get('/merge-pdf')
            ->assertRedirect('/pdf/merge-pdf');
    }

    public function test_merge_pdf_metadata_api_returns_limits(): void
    {
        $this->getJson('/api/tools/pdf/merge-pdf')
            ->assertOk()
            ->assertJsonPath('min_files', 2)
            ->assertJsonPath('exports.0', 'pdf');
    }

    public function test_merge_pdf_api_combines_uploaded_pdfs(): void
    {
        $response = $this->postJson('/api/tools/pdf/merge-pdf/merge', [
            'files' => [
                $this->pdfUpload('one.pdf', 'First PDF'),
                $this->pdfUpload('two.pdf', 'Second PDF'),
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('file.filename', 'toolkitly-merged.pdf')
            ->assertJsonStructure([
                'file' => ['download_url', 'expires_at', 'filename', 'size'],
            ]);

        $this->assertGreaterThan(0, $response->json('file.size'));
    }

    private function pdfUpload(string $name, string $text): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'toolkitly-test-pdf-');

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, $text);
        $pdf->Output('F', $path);

        return new UploadedFile($path, $name, 'application/pdf', null, true);
    }
}
