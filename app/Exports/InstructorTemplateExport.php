<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstructorTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'email',
            'password',
            'nip',
            'jenis_kelamin',
            'tanggal_lahir',
            'no_telepon',
            'alamat',
            'bidang_keahlian',
            'bio',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Ustadz Ahmad',
                'ahmad.guru@example.com',
                'password123',
                '198501012010',
                'L',
                '01/01/1985',
                '081234567890',
                'Jl. Pendidikan No. 1',
                'Matematika',
                'Guru berpengalaman 10 tahun',
            ],
            [
                'Ustadzah Fatimah',
                'fatimah.guru@example.com',
                'password123',
                '199003152012',
                'P',
                '15/03/1990',
                '089876543210',
                'Jl. Pendidikan No. 2',
                'Bahasa Arab',
                'Lulusan Al-Azhar University',
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
                    'startColor' => ['rgb' => '00675C'],
                ],
            ],
        ];
    }
}
