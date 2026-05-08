<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PlatformSettings
{
    public const KEYS = [
        'google_analytics_measurement_id',
        'google_adsense_client',
        'google_adsense_slot_top',
        'google_adsense_slot_middle',
        'google_adsense_slot_bottom',
        'max_upload_kb',
        'temporary_file_ttl',
    ];

    public static function all(): array
    {
        return Cache::rememberForever('toolkitly.platform_settings', function (): array {
            if (! self::tableExists()) {
                return [];
            }

            try {
                return PlatformSetting::query()
                    ->whereIn('key', self::KEYS)
                    ->pluck('value', 'key')
                    ->all();
            } catch (QueryException) {
                return [];
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::all()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function setMany(array $settings): void
    {
        foreach (self::KEYS as $key) {
            if (! array_key_exists($key, $settings)) {
                continue;
            }

            PlatformSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => blank($settings[$key]) ? null : (string) $settings[$key]],
            );
        }

        Cache::forget('toolkitly.platform_settings');
    }

    private static function tableExists(): bool
    {
        try {
            return Schema::hasTable('platform_settings');
        } catch (QueryException) {
            return false;
        }
    }
}
