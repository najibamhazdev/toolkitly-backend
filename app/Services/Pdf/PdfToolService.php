<?php

namespace App\Services\Pdf;

use FPDF;
use App\Support\PlatformSettings;
use Imagick;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class PdfToolService
{
    /**
     * @return array<string, mixed>
     */
    public function metadata(string $tool): array
    {
        $base = [
            'max_upload_kb' => (int) PlatformSettings::get('max_upload_kb', config('toolkitly.max_upload_kb', 10240)),
            'expires_after_seconds' => $this->ttl(),
        ];

        return match ($tool) {
            'split-pdf' => $base + [
                'title' => 'Split PDF',
                'description' => 'Extract every page from one PDF into separate PDF files.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'exports' => ['zip'],
            ],
            'pdf-to-jpg' => $base + [
                'title' => 'PDF to JPG',
                'description' => 'Convert PDF pages into JPG images.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['resolution' => 150, 'quality' => 90],
                'exports' => ['zip'],
            ],
            'jpg-to-pdf' => $base + [
                'title' => 'JPG to PDF',
                'description' => 'Combine JPG images into one PDF file.',
                'accept' => 'image/jpeg,.jpg,.jpeg',
                'max_files' => 20,
                'exports' => ['pdf'],
            ],
            'remove-pdf-pages' => $base + [
                'title' => 'Remove PDF Pages',
                'description' => 'Create a new PDF without the page numbers you choose.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['pages' => '1'],
                'exports' => ['pdf'],
            ],
            'compress-pdf' => $base + [
                'title' => 'Compress PDF',
                'description' => 'Reduce PDF file size using server-side PDF compression.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['quality' => 'ebook'],
                'exports' => ['pdf'],
            ],
            'rotate-pdf' => $base + [
                'title' => 'Rotate PDF',
                'description' => 'Rotate every page in a PDF file.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['angle' => 90],
                'exports' => ['pdf'],
            ],
            'protect-pdf' => $base + [
                'title' => 'Protect PDF',
                'description' => 'Add password protection to a PDF file.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['password' => ''],
                'exports' => ['pdf'],
            ],
            'unlock-pdf' => $base + [
                'title' => 'Unlock PDF',
                'description' => 'Remove a known password from a PDF file.',
                'accept' => 'application/pdf,.pdf',
                'max_files' => 1,
                'defaults' => ['password' => ''],
                'exports' => ['pdf'],
            ],
            default => throw new RuntimeException('Unknown PDF tool.'),
        };
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function split(UploadedFile $file): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('split-pdf', 'toolkitly-split-pages.zip', 'application/zip', function (string $path) use ($file): void {
            $zip = $this->createZip($path);
            $source = new Fpdi();
            $pageCount = $source->setSourceFile($file->getRealPath());

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $pdf = new Fpdi();
                $template = $pdf->setSourceFile($file->getRealPath());
                $page = $pdf->importPage($pageNumber);
                $size = $pdf->getTemplateSize($page);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $tmp = tempnam(sys_get_temp_dir(), 'toolkitly-page-');

                if ($template < 1) {
                    throw new RuntimeException('This PDF could not be split.');
                }

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($page);
                $pdf->Output('F', $tmp);
                $zip->addFile($tmp, 'page-'.$pageNumber.'.pdf');
            }

            $zip->close();
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function pdfToJpg(UploadedFile $file, int $resolution = 150, int $quality = 90): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('pdf-to-jpg', 'toolkitly-pdf-pages.zip', 'application/zip', function (string $path) use ($file, $resolution, $quality): void {
            $zip = $this->createZip($path);
            $images = new Imagick();
            $images->setResolution(max(72, min(300, $resolution)), max(72, min(300, $resolution)));
            $images->readImage($file->getRealPath());

            foreach ($images as $index => $image) {
                $image->setImageFormat('jpeg');
                $image->setImageCompressionQuality(max(40, min(100, $quality)));
                $image->setImageBackgroundColor('white');
                $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
                $zip->addFromString('page-'.($index + 1).'.jpg', $image->getImageBlob());
            }

            $images->clear();
            $zip->close();
        });
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function jpgToPdf(array $files): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('jpg-to-pdf', 'toolkitly-images.pdf', 'application/pdf', function (string $path) use ($files): void {
            $pdf = new FPDF();
            $pdf->SetTitle('ToolKitly JPG to PDF');
            $pdf->SetAuthor('ToolKitly');

            foreach ($files as $file) {
                [$width, $height] = getimagesize($file->getRealPath()) ?: [0, 0];

                if ($width < 1 || $height < 1) {
                    throw new RuntimeException('One of these JPG images could not be read.');
                }

                $pageWidth = $width * 0.264583;
                $pageHeight = $height * 0.264583;
                $orientation = $pageWidth > $pageHeight ? 'L' : 'P';
                $pdf->AddPage($orientation, [$pageWidth, $pageHeight]);
                $pdf->Image($file->getRealPath(), 0, 0, $pageWidth, $pageHeight, 'JPG');
            }

            $pdf->Output('F', $path);
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function removePages(UploadedFile $file, string $pages): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('remove-pdf-pages', 'toolkitly-pages-removed.pdf', 'application/pdf', function (string $path) use ($file, $pages): void {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($file->getRealPath());
            $remove = $this->parsePages($pages, $pageCount);

            if (count($remove) >= $pageCount) {
                throw new RuntimeException('Keep at least one page in the PDF.');
            }

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                if (in_array($pageNumber, $remove, true)) {
                    continue;
                }

                $template = $pdf->importPage($pageNumber);
                $size = $pdf->getTemplateSize($template);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }

            $pdf->Output('F', $path);
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function compress(UploadedFile $file, string $quality = 'ebook'): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('compress-pdf', 'toolkitly-compressed.pdf', 'application/pdf', function (string $path) use ($file, $quality): void {
            $setting = in_array($quality, ['screen', 'ebook', 'printer', 'prepress'], true) ? $quality : 'ebook';
            $this->runGhostscript([
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dPDFSETTINGS=/'.$setting,
                '-dNOPAUSE',
                '-dBATCH',
                '-dQUIET',
                '-sOutputFile='.$path,
                $file->getRealPath(),
            ]);
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function rotate(UploadedFile $file, int $angle = 90): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('rotate-pdf', 'toolkitly-rotated.pdf', 'application/pdf', function (string $path) use ($file, $angle): void {
            $images = new Imagick();
            $images->setResolution(150, 150);
            $images->readImage($file->getRealPath());

            foreach ($images as $image) {
                $image->rotateImage('white', in_array($angle, [90, 180, 270], true) ? $angle : 90);
                $image->setImageFormat('pdf');
            }

            $images->writeImages($path, true);
            $images->clear();
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function protect(UploadedFile $file, string $password): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('protect-pdf', 'toolkitly-protected.pdf', 'application/pdf', function (string $path) use ($file, $password): void {
            $this->runGhostscript([
                '-sDEVICE=pdfwrite',
                '-dNOPAUSE',
                '-dBATCH',
                '-dQUIET',
                '-sOwnerPassword='.$password,
                '-sUserPassword='.$password,
                '-sOutputFile='.$path,
                $file->getRealPath(),
            ]);
        });
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    public function unlock(UploadedFile $file, string $password): array
    {
        $this->cleanupExpiredFiles();

        return $this->storeResult('unlock-pdf', 'toolkitly-unlocked.pdf', 'application/pdf', function (string $path) use ($file, $password): void {
            $this->runGhostscript([
                '-sDEVICE=pdfwrite',
                '-dNOPAUSE',
                '-dBATCH',
                '-dQUIET',
                '-sPDFPassword='.$password,
                '-sOutputFile='.$path,
                $file->getRealPath(),
            ]);
        });
    }

    public function pathFor(string $tool, string $id, string $filename): string
    {
        return $this->storageDirectory($tool, $id).'/'.$filename;
    }

    public function exists(string $tool, string $id, string $filename): bool
    {
        return File::isFile($this->pathFor($tool, $id, $filename));
    }

    public function cleanupExpiredFiles(): void
    {
        File::ensureDirectoryExists($this->baseDirectory());

        foreach (File::directories($this->baseDirectory()) as $toolDirectory) {
            foreach (File::directories($toolDirectory) as $directory) {
                if (File::lastModified($directory) < now()->subSeconds($this->ttl())->timestamp) {
                    File::deleteDirectory($directory);
                }
            }
        }
    }

    /**
     * @return array<int, int>
     */
    private function parsePages(string $pages, int $pageCount): array
    {
        $remove = [];

        foreach (explode(',', $pages) as $part) {
            $part = trim($part);

            if (preg_match('/^(\d+)-(\d+)$/', $part, $matches) === 1) {
                foreach (range((int) $matches[1], (int) $matches[2]) as $page) {
                    $remove[] = $page;
                }

                continue;
            }

            if (ctype_digit($part)) {
                $remove[] = (int) $part;
            }
        }

        $remove = array_values(array_unique(array_filter($remove, fn (int $page): bool => $page >= 1 && $page <= $pageCount)));

        if ($remove === []) {
            throw new RuntimeException('Enter at least one valid page number.');
        }

        return $remove;
    }

    /**
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string, mime: string}
     */
    private function storeResult(string $tool, string $filename, string $mime, callable $callback): array
    {
        $id = (string) Str::uuid();
        $directory = $this->storageDirectory($tool, $id);
        $path = $directory.'/'.$filename;

        File::ensureDirectoryExists($directory);

        try {
            $callback($path);
        } catch (Throwable $exception) {
            File::deleteDirectory($directory);

            throw new RuntimeException('This file could not be processed. Try a different file.', previous: $exception);
        }

        return [
            'id' => $id,
            'filename' => $filename,
            'path' => $path,
            'size' => File::size($path),
            'expires_at' => now()->addSeconds($this->ttl())->toIso8601String(),
            'mime' => $mime,
        ];
    }

    private function createZip(string $path): ZipArchive
    {
        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('The result ZIP could not be created.');
        }

        return $zip;
    }

    /**
     * @param  array<int, string>  $arguments
     */
    private function runGhostscript(array $arguments): void
    {
        $process = new Process(['gs', ...$arguments]);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('Ghostscript could not process this PDF.');
        }
    }

    private function baseDirectory(): string
    {
        return storage_path('app/toolkitly/tmp/pdf-tools');
    }

    private function storageDirectory(string $tool, string $id): string
    {
        return $this->baseDirectory().'/'.$tool.'/'.$id;
    }

    private function ttl(): int
    {
        return (int) PlatformSettings::get('temporary_file_ttl', config('toolkitly.temporary_file_ttl', 3600));
    }
}
