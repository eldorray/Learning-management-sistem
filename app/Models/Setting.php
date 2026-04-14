<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private const CACHE_TTL = 3600;
    private const BULK_CACHE_KEY = 'settings.all';

    /**
     * Get a setting value by key, pulling from the bulk-cached map.
     * A single DB query loads all settings once, then each key hit is O(1) array access.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::all_cached()[$key] ?? $default;
    }

    /**
     * Set a setting value and invalidate the bulk cache.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::BULK_CACHE_KEY);
    }

    /**
     * Return all settings as a key→value array, cached as a single entry.
     */
    public static function all_cached(): array
    {
        return Cache::remember(static::BULK_CACHE_KEY, static::CACHE_TTL, function () {
            return static::pluck('value', 'key')->all();
        });
    }

    /**
     * Flush the single bulk cache entry (called after batch saves).
     */
    public static function clearCache(): void
    {
        Cache::forget(static::BULK_CACHE_KEY);
    }
}
