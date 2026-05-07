<?php

namespace App\Services\ShortLink;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShortLinkService
{
    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return [
            'code_length' => 7,
            'max_url_length' => 2048,
            'expiration_options' => [
                ['value' => '', 'label' => 'Never'],
                ['value' => '1_day', 'label' => '1 day'],
                ['value' => '7_days', 'label' => '7 days'],
                ['value' => '30_days', 'label' => '30 days'],
            ],
        ];
    }

    public function create(string $destinationUrl, ?string $expiration): ShortLink
    {
        return ShortLink::create([
            'code' => $this->uniqueCode(),
            'destination_url' => $destinationUrl,
            'expires_at' => $this->expiresAt($expiration),
        ]);
    }

    public function recordClick(ShortLink $link, Request $request): void
    {
        DB::transaction(function () use ($link, $request): void {
            $link->increment('clicks', 1, [
                'last_clicked_at' => now(),
            ]);

            $link->clicks()->create([
                'ip_hash' => $request->ip() ? hash('sha256', $request->ip().config('app.key')) : null,
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
                'referer' => Str::limit((string) $request->headers->get('referer'), 1000, ''),
                'clicked_at' => now(),
            ]);
        });
    }

    private function uniqueCode(): string
    {
        do {
            $code = Str::lower(Str::random(7));
        } while (ShortLink::where('code', $code)->exists());

        return $code;
    }

    private function expiresAt(?string $expiration): mixed
    {
        return match ($expiration) {
            '1_day' => now()->addDay(),
            '7_days' => now()->addDays(7),
            '30_days' => now()->addDays(30),
            default => null,
        };
    }
}
