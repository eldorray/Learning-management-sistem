<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahfidzRecord extends Model
{
    protected $fillable = [
        'student_id', 'instruktur_id', 'surah_id',
        'ayat_mulai', 'ayat_selesai', 'jenis_setoran',
        'score_kelancaran', 'score_tajwid', 'score_makhorijul_huruf',
        'keterangan', 'status', 'tanggal_setoran',
    ];

    protected $casts = ['tanggal_setoran' => 'date'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    public function getScoreRataRataAttribute(): int
    {
        return (int) round(($this->score_kelancaran + $this->score_tajwid + $this->score_makhorijul_huruf) / 3);
    }

    public function getGradeAttribute(): string
    {
        $avg = $this->score_rata_rata;
        if ($avg >= 90) return 'A';
        if ($avg >= 80) return 'B';
        if ($avg >= 70) return 'C';
        if ($avg >= 60) return 'D';
        return 'E';
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->jenis_setoran === 'ziyadah' ? 'Ziyadah' : 'Murojaah';
    }

    public function getJenisBadgeColorAttribute(): string
    {
        return $this->jenis_setoran === 'ziyadah'
            ? 'bg-[#73f2dd]/30 text-[#00675c]'
            : 'bg-[#6c9fff]/20 text-[#0058ba]';
    }
}
