<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => '2023/2024',
                'semester' => '1',
                'tanggal_mulai' => '2023-07-17',
                'tanggal_selesai' => '2023-12-21',
                'is_aktif' => false,
            ],
            [
                'nama' => '2023/2024',
                'semester' => '2',
                'tanggal_mulai' => '2024-01-08',
                'tanggal_selesai' => '2024-06-20',
                'is_aktif' => false,
            ],
            [
                'nama' => '2024/2025',
                'semester' => '1',
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2024-12-19',
                'is_aktif' => false,
            ],
            [
                'nama' => '2024/2025',
                'semester' => '2',
                'tanggal_mulai' => '2025-01-06',
                'tanggal_selesai' => '2025-06-19',
                'is_aktif' => false,
            ],
            [
                'nama' => '2025/2026',
                'semester' => '1',
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2025-12-18',
                'is_aktif' => false,
            ],
            [
                'nama' => '2025/2026',
                'semester' => '2',
                'tanggal_mulai' => '2026-01-05',
                'tanggal_selesai' => '2026-06-18',
                'is_aktif' => true, // AKTIF SAAT INI
                'keterangan' => 'Tahun ajaran aktif saat ini',
            ],
        ];

        foreach ($data as $row) {
            TahunAjaran::firstOrCreate(
                ['nama' => $row['nama'], 'semester' => $row['semester']],
                $row
            );
        }
    }
}
