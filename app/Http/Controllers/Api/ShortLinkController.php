<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use App\Services\ShortLink\ShortLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShortLinkController extends Controller
{
    public function __construct(private readonly ShortLinkService $shortLinks) {}

    public function show(): JsonResponse
    {
        return response()->json($this->shortLinks->metadata());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'destination_url' => ['required', 'url:http,https', 'max:2048'],
            'expiration' => ['nullable', Rule::in(['', '1_day', '7_days', '30_days'])],
        ]);

        $link = $this->shortLinks->create($data['destination_url'], $data['expiration'] ?? null);

        return response()->json([
            'link' => $this->resource($link),
        ], 201);
    }

    public function stats(string $code): JsonResponse
    {
        $link = ShortLink::where('code', $code)->firstOrFail();

        return response()->json([
            'link' => $this->resource($link),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resource(ShortLink $link): array
    {
        return [
            'code' => $link->code,
            'destination_url' => $link->destination_url,
            'short_url' => url('/s/'.$link->code),
            'clicks' => $link->clicks,
            'expires_at' => $link->expires_at?->toIso8601String(),
            'last_clicked_at' => $link->last_clicked_at?->toIso8601String(),
            'is_expired' => $link->isExpired(),
        ];
    }
}
