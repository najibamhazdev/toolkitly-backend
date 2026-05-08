<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToolEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ToolEventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tool' => ['required', 'string', 'max:100'],
            'action' => ['required', 'string', 'max:60'],
            'status' => ['nullable', 'in:success,error'],
            'metadata' => ['nullable', 'array'],
            'visitor_id' => ['nullable', 'string', 'max:120'],
        ]);

        ToolEvent::create([
            'tool' => $data['tool'],
            'action' => $data['action'],
            'status' => $data['status'] ?? 'success',
            'metadata' => Arr::only($data['metadata'] ?? [], ['format', 'type', 'count', 'mode']),
            'visitor_hash' => $this->hashValue($data['visitor_id'] ?? null),
            'ip_hash' => $this->hashValue($request->ip()),
            'user_agent_hash' => $this->hashValue($request->userAgent()),
        ]);

        return response()->json(['ok' => true], 201);
    }

    private function hashValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return hash_hmac('sha256', $value, config('app.key'));
    }
}
