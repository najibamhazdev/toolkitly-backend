<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Barcode\BarcodePayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarcodeController extends Controller
{
    public function __construct(private readonly BarcodePayloadService $barcodes) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'types' => $this->barcodes->types(),
            'defaults' => $this->barcodes->defaults(),
            'exports' => ['png', 'svg'],
        ]);
    }

    public function payload(Request $request): JsonResponse
    {
        $type = (string) $request->input('type', 'code128');

        $data = $request->validate([
            'type' => ['required', Rule::in(['code128', 'ean13', 'upc'])],
            'text' => ['required', 'string', 'max:128', 'regex:/^'.$this->barcodes->pattern($type).'$/'],
            'scale' => ['nullable', 'integer', 'min:1', 'max:6'],
            'height' => ['nullable', 'integer', 'min:8', 'max:40'],
            'include_text' => ['nullable', 'boolean'],
        ]);

        return response()->json([
            'barcode' => [
                ...$this->barcodes->make($data['type'], $data['text']),
                'scale' => (int) ($data['scale'] ?? 3),
                'height' => (int) ($data['height'] ?? 12),
                'include_text' => (bool) ($data['include_text'] ?? true),
            ],
        ]);
    }
}
