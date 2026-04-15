<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    protected $fillable = [
        'nomor', 'nama_arab', 'nama_latin', 'nama_indonesia',
        'jumlah_ayat', 'juz', 'tempat_turun',
    ];

    public function tahfidzRecords(): HasMany
    {
        return $this->hasMany(TahfidzRecord::class);
    }

    public function tahfidzTargetsMulai(): HasMany
    {
        return $this->hasMany(TahfidzTarget::class, 'surah_mulai_id');
    }

    public function tahfidzTargetsSelesai(): HasMany
    {
        return $this->hasMany(TahfidzTarget::class, 'surah_selesai_id');
    }

    public function getScoreRataAttribute(int $studentId): int
    {
        $records = $this->tahfidzRecords()->where('student_id', $studentId)->get();
        if ($records->isEmpty()) return 0;
        return (int) round($records->avg(fn($r) => ($r->score_kelancaran + $r->score_tajwid + $r->score_makhorijul_huruf) / 3));
    }
}
