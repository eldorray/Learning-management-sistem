<?php

namespace Database\Seeders;

use App\Models\Surah;
use App\Models\TahfidzGroup;
use App\Models\TahfidzRecord;
use App\Models\TahfidzTarget;
use App\Models\User;
use Illuminate\Database\Seeder;

class TahfidzSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', 'instructor')->get();
        $students    = User::where('role', 'student')->get();

        if ($instructors->isEmpty() || $students->isEmpty()) return;

        // ── Tahfidz Targets (per kelas) ──────────────────────────────
        $surah67 = Surah::where('nomor', 67)->first(); // Al-Mulk
        $surah78 = Surah::where('nomor', 78)->first(); // An-Naba
        $surah114= Surah::where('nomor', 114)->first(); // An-Nas

        if ($surah67 && $surah114) {
            TahfidzTarget::create([
                'tingkat_kelas'   => 'Kelas 7',
                'semester'        => '1',
                'surah_mulai_id'  => $surah78->id,
                'ayat_mulai'      => 1,
                'surah_selesai_id'=> $surah114->id,
                'ayat_selesai'    => $surah114->jumlah_ayat,
                'keterangan'      => 'Target Juz 30 — Kelas 7 Semester 1',
            ]);

            TahfidzTarget::create([
                'tingkat_kelas'   => 'Kelas 8',
                'semester'        => '1',
                'surah_mulai_id'  => $surah67->id,
                'ayat_mulai'      => 1,
                'surah_selesai_id'=> $surah114->id,
                'ayat_selesai'    => $surah114->jumlah_ayat,
                'keterangan'      => 'Target Juz 29-30 — Kelas 8 Semester 1',
            ]);
        }

        // ── Halaqoh Groups ────────────────────────────────────────────
        $group1 = TahfidzGroup::create([
            'instruktur_id' => $instructors->first()->id,
            'nama_halaqoh'  => 'Halaqoh Al-Fatih',
            'tingkat_kelas' => 'Kelas 7',
            'deskripsi'     => 'Halaqoh untuk siswa kelas 7 - target Juz 30',
            'is_active'     => true,
        ]);

        $group2 = TahfidzGroup::create([
            'instruktur_id' => $instructors->count() > 1 ? $instructors->last()->id : $instructors->first()->id,
            'nama_halaqoh'  => 'Halaqoh Az-Zahra',
            'tingkat_kelas' => 'Kelas 8',
            'deskripsi'     => 'Halaqoh untuk siswa kelas 8 - target Juz 29-30',
            'is_active'     => true,
        ]);

        // Assign students to groups
        $half = (int) ceil($students->count() / 2);
        foreach ($students->take($half) as $student) {
            $group1->students()->attach($student->id, ['joined_at' => now()->subDays(30)]);
        }
        foreach ($students->skip($half) as $student) {
            $group2->students()->attach($student->id, ['joined_at' => now()->subDays(25)]);
        }

        // ── Sample Tahfidz Records ────────────────────────────────────
        $sampleSurahs = Surah::whereBetween('nomor', [78, 114])->get();
        if ($sampleSurahs->isEmpty()) return;

        $instruktur = $instructors->first();

        foreach ($students as $student) {
            $setoranCount = rand(3, 8);
            for ($i = 0; $i < $setoranCount; $i++) {
                $surah = $sampleSurahs->random();
                $ayatMulai   = rand(1, max(1, $surah->jumlah_ayat - 5));
                $ayatSelesai = min($surah->jumlah_ayat, $ayatMulai + rand(3, 10));

                TahfidzRecord::create([
                    'student_id'            => $student->id,
                    'instruktur_id'         => $instruktur->id,
                    'surah_id'              => $surah->id,
                    'ayat_mulai'            => $ayatMulai,
                    'ayat_selesai'          => $ayatSelesai,
                    'jenis_setoran'         => $i % 3 === 0 ? 'murojaah' : 'ziyadah',
                    'score_kelancaran'      => rand(70, 100),
                    'score_tajwid'          => rand(65, 100),
                    'score_makhorijul_huruf'=> rand(70, 100),
                    'keterangan'            => $i === 0 ? 'Bacaan sudah baik, perhatikan ghunnah.' : null,
                    'status'                => 'approved',
                    'tanggal_setoran'       => now()->subDays(rand(0, 60))->format('Y-m-d'),
                ]);
            }
        }
    }
}
