<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class HttpHeaderCheckerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        try {
            $response = Http::timeout(10)
                ->withOptions(['allow_redirects' => false])
                ->get($data['url']);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'url' => ['This URL could not be reached.'],
            ]);
        }

        return response()->json([
            'url' => $data['url'],
            'status' => $response->status(),
            'headers' => collect($response->headers())
                ->map(fn (array $values): string => implode(', ', $values))
                ->all(),
        ]);
    }
}
