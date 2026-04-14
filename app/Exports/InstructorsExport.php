<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstructorsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected string $search;
    protected string $filterGender;
    protected string $filterSpecialization;

    public function __construct(string $search = '', string $filterGender = '', string $filterSpecialization = '')
    {
        $this->search = $search;
        $this->filterGender = $filterGender;
        $this->filterSpecialization = $filterSpecialization;
    }

    public function query()
    {
        $query = User::where('role', 'instructor');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGender) {
            $query->where('gender', $this->filterGender);
        }

        if ($this->filterSpecialization) {
            $query->where('specialization', $this->filterSpecialization);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'No. Telepon',
            'Alamat',
            'Bidang Keahlian',
            'Bio',
            'Tanggal Registrasi',
        ];
    }

    public function map($instructor): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $instructor->nip ?? '-',
            $instructor->name,
            $instructor->email,
            $instructor->gender === 'L' ? 'Laki-laki' : ($instructor->gender === 'P' ? 'Perempuan' : '-'),
            $instructor->birth_date ? $instructor->birth_date->format('d/m/Y') : '-',
            $instructor->phone ?? '-',
            $instructor->address ?? '-',
            $instructor->specialization ?? '-',
            $instructor->bio ?? '-',
            $instructor->created_at->format('d/m/Y'),
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
