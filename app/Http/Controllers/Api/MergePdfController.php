<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pdf\MergePdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class MergePdfController extends Controller
{
    public function __construct(private readonly MergePdfService $pdfs) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'min_files' => 2,
            'max_files' => 20,
            'max_upload_kb' => (int) config('toolkitly.max_upload_kb', 10240),
            'expires_after_seconds' => (int) config('toolkitly.temporary_file_ttl', 3600),
            'exports' => ['pdf'],
        ]);
    }

    public function merge(Request $request): JsonResponse
    {
        $maxUploadKb = (int) config('toolkitly.max_upload_kb', 10240);

        $data = $request->validate([
            'files' => ['required', 'array', 'min:2', 'max:20'],
            'files.*' => ['required', 'file', 'mimetypes:application/pdf', 'max:'.$maxUploadKb],
        ]);

        try {
            $result = $this->pdfs->merge($data['files']);
        } catch (Throwable $exception) {
            throw ValidationException::withMessages([
                'files' => [$exception->getMessage()],
            ]);
        }

        return response()->json([
            'file' => [
                'filename' => $result['filename'],
                'size' => $result['size'],
                'expires_at' => $result['expires_at'],
                'download_url' => route('tools.merge-pdf.download', ['id' => $result['id']]),
            ],
        ]);
    }

    public function download(string $id): BinaryFileResponse
    {
        abort_unless(preg_match('/^[0-9a-f-]{36}$/', $id) === 1, 404);

        $this->pdfs->cleanupExpiredFiles();

        abort_unless($this->pdfs->exists($id), 404);

        return Response::download($this->pdfs->pathFor($id), 'toolkitly-merged.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
