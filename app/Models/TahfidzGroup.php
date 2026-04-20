<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TahfidzGroup extends Model
{
    protected $fillable = [
        'instruktur_id', 'nama_halaqoh', 'tingkat_kelas', 'deskripsi', 'is_active', 'tahun_ajaran_id',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tahfidz_group_students', 'tahfidz_group_id', 'student_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }
}
