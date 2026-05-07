<?php

namespace App\Services\Pdf;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use Throwable;

class MergePdfService
{
    /**
     * @param  array<int, UploadedFile>  $files
     * @return array{id: string, filename: string, path: string, size: int, expires_at: string}
     */
    public function merge(array $files): array
    {
        $this->cleanupExpiredFiles();

        if (count($files) < 2) {
            throw new RuntimeException('Upload at least two PDF files.');
        }

        $id = (string) Str::uuid();
        $directory = $this->storageDirectory($id);
        $filename = 'toolkitly-merged.pdf';
        $path = $directory.'/'.$filename;

        File::ensureDirectoryExists($directory);

        $pdf = new Fpdi();
        $pdf->SetTitle('ToolKitly merged PDF');
        $pdf->SetAuthor('ToolKitly');

        try {
            foreach ($files as $file) {
                $pageCount = $pdf->setSourceFile($file->getRealPath());

                for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                    $template = $pdf->importPage($pageNumber);
                    $size = $pdf->getTemplateSize($template);
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($template);
                }
            }

            $pdf->Output('F', $path);
        } catch (Throwable $exception) {
            File::deleteDirectory($directory);

            throw new RuntimeException('One of these PDFs could not be merged. Try files that are not encrypted or password protected.', previous: $exception);
        }

        return [
            'id' => $id,
            'filename' => $filename,
            'path' => $path,
            'size' => File::size($path),
            'expires_at' => now()->addSeconds($this->ttl())->toIso8601String(),
        ];
    }

    public function pathFor(string $id): string
    {
        return $this->storageDirectory($id).'/toolkitly-merged.pdf';
    }

    public function exists(string $id): bool
    {
        return File::isFile($this->pathFor($id));
    }

    public function cleanupExpiredFiles(): void
    {
        File::ensureDirectoryExists($this->baseDirectory());

        foreach (File::directories($this->baseDirectory()) as $directory) {
            if (File::lastModified($directory) < now()->subSeconds($this->ttl())->timestamp) {
                File::deleteDirectory($directory);
            }
        }
    }

    private function baseDirectory(): string
    {
        return storage_path('app/toolkitly/tmp/merge-pdf');
    }

    private function storageDirectory(string $id): string
    {
        return $this->baseDirectory().'/'.$id;
    }

    private function ttl(): int
    {
        return (int) config('toolkitly.temporary_file_ttl', 3600);
    }
}
