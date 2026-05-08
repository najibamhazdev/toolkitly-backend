<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RedirectCheckerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $url = $data['url'];
        $chain = [];

        for ($hop = 0; $hop < 10; $hop++) {
            try {
                $response = Http::timeout(10)
                    ->withOptions(['allow_redirects' => false])
                    ->get($url);
            } catch (ConnectionException) {
                throw ValidationException::withMessages([
                    'url' => ['This URL could not be reached.'],
                ]);
            }

            $status = $response->status();
            $location = $response->header('Location');
            $chain[] = [
                'url' => $url,
                'status' => $status,
                'location' => $location,
            ];

            if ($location === null || $status < 300 || $status >= 400) {
                break;
            }

            $url = $this->absoluteUrl($url, $location);
        }

        return response()->json([
            'final_url' => end($chain)['url'],
            'hops' => count($chain) - 1,
            'chain' => $chain,
        ]);
    }

    private function absoluteUrl(string $base, string $location): string
    {
        if (filter_var($location, FILTER_VALIDATE_URL)) {
            return $location;
        }

        $parts = parse_url($base);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';

        if (str_starts_with($location, '/')) {
            return "{$scheme}://{$host}{$location}";
        }

        $path = isset($parts['path']) ? dirname($parts['path']) : '';

        return "{$scheme}://{$host}{$path}/{$location}";
    }
}
