<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahfidzTarget extends Model
{
    protected $fillable = [
        'tingkat_kelas', 'semester', 'surah_mulai_id', 'ayat_mulai',
        'surah_selesai_id', 'ayat_selesai', 'keterangan',
    ];

    public function surahMulai(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'surah_mulai_id');
    }

    public function surahSelesai(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'surah_selesai_id');
    }

    public function getLabelAttribute(): string
    {
        if ($this->surahMulai && $this->surahSelesai) {
            if ($this->surah_mulai_id === $this->surah_selesai_id) {
                return "Qs. {$this->surahMulai->nama_latin} ({$this->ayat_mulai}-{$this->ayat_selesai})";
            }
            return "Qs. {$this->surahMulai->nama_latin} s/d {$this->surahSelesai->nama_latin}";
        }
        return '-';
    }
}
