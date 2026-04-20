<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_aktif', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_aktif' => 'boolean',
    ];

    /**
     * Get the currently active academic year.
     */
    public static function aktif(): ?static
    {
        return static::where('is_aktif', true)->first();
    }

    /**
     * Activate this academic year and deactivate all others.
     */
    public function aktifkan(): void
    {
        static::where('id', '!=', $this->id)->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nama} Semester {$this->semester}";
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function tahfidzGroups(): HasMany
    {
        return $this->hasMany(TahfidzGroup::class);
    }

    public function tahfidzTargets(): HasMany
    {
        return $this->hasMany(TahfidzTarget::class);
    }

    public function tahfidzRecords(): HasMany
    {
        return $this->hasMany(TahfidzRecord::class);
    }
}
