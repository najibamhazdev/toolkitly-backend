<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pdf\PdfToolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class PdfToolController extends Controller
{
    public function __construct(private readonly PdfToolService $pdfs) {}

    public function show(string $tool): JsonResponse
    {
        abort_unless($this->isSupportedTool($tool), 404);

        return response()->json($this->pdfs->metadata($tool));
    }

    public function process(Request $request, string $tool): JsonResponse
    {
        abort_unless($this->isSupportedTool($tool), 404);

        $maxUploadKb = (int) config('toolkitly.max_upload_kb', 10240);

        $data = match ($tool) {
            'split-pdf', 'pdf-to-jpg', 'remove-pdf-pages', 'compress-pdf', 'rotate-pdf', 'protect-pdf', 'unlock-pdf' => $request->validate([
                'file' => ['required', 'file', 'mimetypes:application/pdf', 'max:'.$maxUploadKb],
                'pages' => ['sometimes', 'string', 'max:120'],
                'resolution' => ['sometimes', 'integer', 'min:72', 'max:300'],
                'quality' => ['sometimes', 'integer', 'min:40', 'max:100'],
                'compression' => ['sometimes', 'string', 'in:screen,ebook,printer,prepress'],
                'angle' => ['sometimes', 'integer', 'in:90,180,270'],
                'password' => ['sometimes', 'string', 'min:1', 'max:120'],
            ]),
            'jpg-to-pdf' => $request->validate([
                'files' => ['required', 'array', 'min:1', 'max:20'],
                'files.*' => ['required', 'file', 'mimetypes:image/jpeg,image/jpg', 'max:'.$maxUploadKb],
            ]),
        };

        try {
            $result = match ($tool) {
                'split-pdf' => $this->pdfs->split($data['file']),
                'pdf-to-jpg' => $this->pdfs->pdfToJpg($data['file'], (int) ($data['resolution'] ?? 150), (int) ($data['quality'] ?? 90)),
                'jpg-to-pdf' => $this->pdfs->jpgToPdf($data['files']),
                'remove-pdf-pages' => $this->pdfs->removePages($data['file'], $data['pages'] ?? '1'),
                'compress-pdf' => $this->pdfs->compress($data['file'], $data['compression'] ?? 'ebook'),
                'rotate-pdf' => $this->pdfs->rotate($data['file'], (int) ($data['angle'] ?? 90)),
                'protect-pdf' => $this->pdfs->protect($data['file'], $data['password'] ?? ''),
                'unlock-pdf' => $this->pdfs->unlock($data['file'], $data['password'] ?? ''),
            };
        } catch (Throwable $exception) {
            throw ValidationException::withMessages([
                'file' => [$exception->getMessage()],
            ]);
        }

        return response()->json([
            'file' => [
                'filename' => $result['filename'],
                'size' => $result['size'],
                'expires_at' => $result['expires_at'],
                'download_url' => route('tools.pdf.download', [
                    'tool' => $tool,
                    'id' => $result['id'],
                    'filename' => $result['filename'],
                ]),
            ],
        ]);
    }

    public function download(string $tool, string $id, string $filename): BinaryFileResponse
    {
        abort_unless($this->isSupportedTool($tool), 404);
        abort_unless(preg_match('/^[0-9a-f-]{36}$/', $id) === 1, 404);
        abort_unless(preg_match('/^[a-z0-9.-]+$/', $filename) === 1, 404);

        $this->pdfs->cleanupExpiredFiles();

        abort_unless($this->pdfs->exists($tool, $id, $filename), 404);

        return Response::download($this->pdfs->pathFor($tool, $id, $filename), $filename);
    }

    private function isSupportedTool(string $tool): bool
    {
        return in_array($tool, ['split-pdf', 'pdf-to-jpg', 'jpg-to-pdf', 'remove-pdf-pages', 'compress-pdf', 'rotate-pdf', 'protect-pdf', 'unlock-pdf'], true);
    }
}
