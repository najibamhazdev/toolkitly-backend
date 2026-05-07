<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QRCode\QRCodePayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QRCodeController extends Controller
{
    public function __construct(private readonly QRCodePayloadService $payloads) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'types' => $this->payloads->types(),
            'defaults' => $this->payloads->defaults(),
            'exports' => ['png', 'svg'],
        ]);
    }

    public function payload(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['url', 'wifi', 'vcard', 'text'])],
            'url' => ['nullable', 'string', 'max:2000'],
            'text' => ['nullable', 'string', 'max:2000'],
            'wifi.ssid' => ['nullable', 'string', 'max:128'],
            'wifi.password' => ['nullable', 'string', 'max:128'],
            'wifi.encryption' => ['nullable', Rule::in(['WPA', 'WEP', 'nopass'])],
            'wifi.hidden' => ['nullable', 'boolean'],
            'vcard.first_name' => ['nullable', 'string', 'max:80'],
            'vcard.last_name' => ['nullable', 'string', 'max:80'],
            'vcard.organization' => ['nullable', 'string', 'max:120'],
            'vcard.title' => ['nullable', 'string', 'max:120'],
            'vcard.phone' => ['nullable', 'string', 'max:40'],
            'vcard.email' => ['nullable', 'email', 'max:160'],
            'vcard.website' => ['nullable', 'string', 'max:2000'],
        ]);

        return response()->json([
            'payload' => $this->payloads->make($data),
        ]);
    }
}
