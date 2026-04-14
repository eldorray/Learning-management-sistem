<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'email',
            'password',
            'nis',
            'jenis_kelamin',
            'tanggal_lahir',
            'no_telepon',
            'alamat',
            'kelas',
            'nama_wali',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Ahmad Fauzan',
                'ahmad@example.com',
                'password123',
                '2024001',
                'L',
                '15/06/2010',
                '081234567890',
                'Jl. Contoh No. 1',
                'VII-A',
                'Budi Utomo',
            ],
            [
                'Siti Nurhaliza',
                'siti@example.com',
                'password123',
                '2024002',
                'P',
                '22/03/2011',
                '089876543210',
                'Jl. Contoh No. 2',
                'VII-B',
                'Dewi Lestari',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0058BA'],
                ],
            ],
        ];
    }
}
