<?php

namespace App\Support;

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Cache;

class AcademicYear
{
    /**
     * Get the currently active TahunAjaran model.
     * Caches only the ID to avoid __PHP_Incomplete_Class on deserialization.
     */
    public static function aktif(): ?TahunAjaran
    {
        $id = Cache::remember('tahun_ajaran.aktif_id', 300, function () {
            return TahunAjaran::where('is_aktif', true)->value('id');
        });

        return $id ? TahunAjaran::find($id) : null;
    }

    /**
     * Get the ID of the active academic year (or null).
     */
    public static function aktifId(): ?int
    {
        return Cache::remember('tahun_ajaran.aktif_id', 300, function () {
            return TahunAjaran::where('is_aktif', true)->value('id');
        });
    }

    /**
     * Get label like "2025/2026 Semester 2".
     */
    public static function label(): string
    {
        $ta = static::aktif();
        return $ta ? "{$ta->nama} Semester {$ta->semester}" : '—';
    }

    /**
     * Invalidate cache (call after changing active year).
     */
    public static function clearCache(): void
    {
        Cache::forget('tahun_ajaran.aktif_id');
    }

    /**
     * Apply scope: filter query by active tahun_ajaran_id.
     * If no active year exists, no filter is applied.
     */
    public static function scopeQuery(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $id = static::aktifId();
        if ($id) {
            $query->where('tahun_ajaran_id', $id);
        }
        return $query;
    }
}
