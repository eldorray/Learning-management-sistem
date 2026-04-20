<?php

use App\Models\Surah;
use App\Models\TahfidzRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeTahfidzRecord(array $overrides = []): TahfidzRecord
{
    return new TahfidzRecord(array_merge([
        'score_kelancaran' => 80,
        'score_tajwid' => 90,
        'score_makhorijul_huruf' => 70,
        'jenis_setoran' => 'ziyadah',
        'status' => 'approved',
    ], $overrides));
}

test('score rata-rata dihitung dengan benar', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 80,
        'score_tajwid' => 90,
        'score_makhorijul_huruf' => 70,
    ]);

    // (80 + 90 + 70) / 3 = 80
    expect($record->score_rata_rata)->toBe(80);
});

test('grade A jika score rata-rata >= 90', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 90,
        'score_tajwid' => 95,
        'score_makhorijul_huruf' => 90,
    ]);

    expect($record->grade)->toBe('A');
});

test('grade B jika score rata-rata >= 80', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 80,
        'score_tajwid' => 85,
        'score_makhorijul_huruf' => 80,
    ]);

    expect($record->grade)->toBe('B');
});

test('grade C jika score rata-rata >= 70', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 70,
        'score_tajwid' => 75,
        'score_makhorijul_huruf' => 70,
    ]);

    expect($record->grade)->toBe('C');
});

test('grade D jika score rata-rata >= 60', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 60,
        'score_tajwid' => 65,
        'score_makhorijul_huruf' => 60,
    ]);

    expect($record->grade)->toBe('D');
});

test('grade E jika score rata-rata di bawah 60', function () {
    $record = makeTahfidzRecord([
        'score_kelancaran' => 50,
        'score_tajwid' => 55,
        'score_makhorijul_huruf' => 50,
    ]);

    expect($record->grade)->toBe('E');
});

test('jenis label ziyadah menampilkan Ziyadah', function () {
    $record = makeTahfidzRecord(['jenis_setoran' => 'ziyadah']);
    expect($record->jenis_label)->toBe('Ziyadah');
});

test('jenis label murojaah menampilkan Murojaah', function () {
    $record = makeTahfidzRecord(['jenis_setoran' => 'murojaah']);
    expect($record->jenis_label)->toBe('Murojaah');
});

test('jenis badge color berbeda untuk ziyadah dan murojaah', function () {
    $ziyadah = makeTahfidzRecord(['jenis_setoran' => 'ziyadah']);
    $murojaah = makeTahfidzRecord(['jenis_setoran' => 'murojaah']);

    expect($ziyadah->jenis_badge_color)->toContain('00675c');
    expect($murojaah->jenis_badge_color)->toContain('0058ba');
});

test('tahfidz record memiliki relasi ke student', function () {
    $student = User::factory()->create(['role' => 'student']);
    $instructor = User::factory()->create(['role' => 'instructor']);
    $surah = Surah::create([
        'nomor' => 1,
        'nama_arab' => 'الفاتحة',
        'nama_latin' => 'Al-Fatihah',
        'nama_indonesia' => 'Pembukaan',
        'jumlah_ayat' => 7,
        'juz' => 1,
        'tempat_turun' => 'makkiyah',
    ]);

    $record = TahfidzRecord::create([
        'student_id' => $student->id,
        'instruktur_id' => $instructor->id,
        'surah_id' => $surah->id,
        'ayat_mulai' => 1,
        'ayat_selesai' => 7,
        'jenis_setoran' => 'ziyadah',
        'score_kelancaran' => 85,
        'score_tajwid' => 90,
        'score_makhorijul_huruf' => 80,
        'status' => 'approved',
        'tanggal_setoran' => now(),
    ]);

    expect($record->student->id)->toBe($student->id);
    expect($record->instruktur->id)->toBe($instructor->id);
    expect($record->surah->id)->toBe($surah->id);
});
